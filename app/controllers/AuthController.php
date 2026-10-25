<?php

class AuthController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->library('api');
    }

    /**
     * POST /api/auth/register
     */
    public function register()
    {
        $this->api->require_method('POST');
        $this->api->rate_limit();

        $input = $this->api->body();

        $studentNo = trim((string) ($input['student_no'] ?? ''));
        $fullName  = trim((string) ($input['full_name'] ?? ''));
        $email     = strtolower(trim((string) ($input['email'] ?? '')));
        $password  = (string) ($input['password'] ?? '');

        if ($fullName === '' || $email === '' || $password === '') {
            $this->api->respond_error(
                'Full name, email, and password are required.',
                422
            );
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->api->respond_error('Invalid email address.', 422);
        }

        if (strlen($password) < 8) {
            $this->api->respond_error(
                'Password must be at least 8 characters.',
                422
            );
        }

        // Check whether the email is already registered.
        $existingEmail = $this->db->raw(
            'SELECT user_id FROM users WHERE email = ? LIMIT 1',
            [$email]
        )->fetch(PDO::FETCH_ASSOC);

        if ($existingEmail) {
            $this->api->respond_error(
                'This email is already registered.',
                409
            );
        }

        // An empty student number is stored as NULL.
        $studentNo = $studentNo === '' ? null : $studentNo;

        if ($studentNo !== null) {
            $existingStudentNo = $this->db->raw(
                'SELECT user_id FROM users WHERE student_no = ? LIMIT 1',
                [$studentNo]
            )->fetch(PDO::FETCH_ASSOC);

            if ($existingStudentNo) {
                $this->api->respond_error(
                    'This student number is already registered.',
                    409
                );
            }
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        if ($passwordHash === false) {
            $this->api->respond_error(
                'Unable to create the account right now.',
                500
            );
        }

        // Public registration always creates a student account.
        // Never accept role or status from the request body.
        $this->db->raw(
            'INSERT INTO users
                (student_no, full_name, email, password_hash, role, status)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $studentNo,
                $fullName,
                $email,
                $passwordHash,
                'student',
                'active'
            ]
        );

        $user = $this->db->raw(
            'SELECT user_id, student_no, full_name, email, role, status,
                    section_id, yr_id, created_at
             FROM users
             WHERE email = ?
             LIMIT 1',
            [$email]
        )->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $this->api->respond_error(
                'Account created, but the user record could not be loaded.',
                500
            );
        }

        $this->api->respond([
            'message' => 'Registration successful.',
            'user' => $user
        ], 201);
    }

    /**
     * POST /api/auth/login
     */
    public function login()
    {
        $this->api->require_method('POST');
        $this->api->rate_limit();

        $input = $this->api->body();

        $email    = strtolower(trim((string) ($input['email'] ?? '')));
        $password = (string) ($input['password'] ?? '');

        if ($email === '' || $password === '') {
            $this->api->respond_error(
                'Email and password are required.',
                422
            );
        }

        $user = $this->db->raw(
            'SELECT user_id, student_no, full_name, email, password_hash,
                    role, status, section_id, yr_id, created_at
             FROM users
             WHERE email = ?
             LIMIT 1',
            [$email]
        )->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->api->respond_error(
                'Invalid email or password.',
                401
            );
        }

        if ($user['status'] !== 'active') {
            $this->api->respond_error(
                'This account is inactive. Please contact the administrator.',
                403
            );
        }

        // Use user_id as the JWT subject, not the default "id" column.
        $accessToken = $this->api->encode_jwt([
            'sub'  => (string) $user['user_id'],
            'role' => $user['role']
        ]);

        unset($user['password_hash']);

        $this->api->respond([
            'message' => 'Login successful.',
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 900,
            'user' => $user
        ]);
    }

    /**
     * GET /api/auth/me
     */
    public function me()
    {
        $this->api->require_method('GET');

        // Validates the JWT signature and standard claims.
        $auth = $this->api->require_jwt();

        $userId = $auth['sub'] ?? null;

        if ($userId === null || !ctype_digit((string) $userId)) {
            $this->api->respond_error('Invalid token subject.', 401);
        }

        // Manually verify the user because the PK is user_id.
        $user = $this->db->raw(
            'SELECT user_id, student_no, full_name, email, role, status,
                    section_id, yr_id, created_at
             FROM users
             WHERE user_id = ?
             LIMIT 1',
            [(int) $userId]
        )->fetch(PDO::FETCH_ASSOC);

        if (!$user || $user['status'] !== 'active') {
            $this->api->respond_error(
                'User account not found or inactive.',
                401
            );
        }

        // Use the current database role, not just the token role.
        $this->api->respond([
            'user' => $user
        ]);
    }
}
