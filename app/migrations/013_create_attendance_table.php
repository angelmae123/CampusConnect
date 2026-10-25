<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Create_attendance_table
{
    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        if ($this->_lava->dbforge->table_exists('attendance')) {
            return;
        }

        $this->_lava->dbforge
            ->add_field([
                'attendance_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                    'null'           => FALSE
                ],
                'event_reg_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => TRUE,
                    'null'       => FALSE
                ],
                'scanned_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => TRUE,
                    'null'       => TRUE
                ],
                // The database stamps the scan time; the app never sends it.
                'scanned_at' => [
                    'type'    => 'TIMESTAMP',
                    'null'    => TRUE,
                    'default' => 'CURRENT_TIMESTAMP'
                ],
                // ENUM values go inside the type string: DBForge cannot turn an array 'constraint' into SQL.
                'status' => [
                    'type'    => "ENUM('present','late')",
                    'default' => 'present',
                    'null'    => FALSE
                ]
            ])
            ->add_key('attendance_id', TRUE)
            ->create_table('attendance');

        // One check-in per registration.
        $this->_lava->db->raw("
            ALTER TABLE attendance
            ADD UNIQUE KEY uq_attendance_event_reg (event_reg_id)
        ");

        // Foreign key: attendance.event_reg_id -> event_registrations.event_reg_id
        $this->_lava->db->raw("
            ALTER TABLE attendance
            ADD CONSTRAINT fk_attendance_event_registration
            FOREIGN KEY (event_reg_id)
            REFERENCES event_registrations(event_reg_id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
        ");

        // Foreign key: attendance.scanned_by -> users.user_id
        $this->_lava->db->raw("
            ALTER TABLE attendance
            ADD CONSTRAINT fk_attendance_scanned_by
            FOREIGN KEY (scanned_by)
            REFERENCES users(user_id)
            ON UPDATE CASCADE
            ON DELETE SET NULL
        ");
    }

    public function down()
    {
        $this->_lava->dbforge->drop_table('attendance', TRUE);
    }
}