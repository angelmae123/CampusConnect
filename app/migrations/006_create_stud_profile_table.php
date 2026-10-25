
<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Create_stud_profile_table
{
    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        if ($this->_lava->dbforge->table_exists('student_profiles')) {
            return;
        }

        $this->_lava->dbforge
            ->add_field([
                'profile_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                    'null'           => FALSE
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => TRUE,
                    'null'       => FALSE
                ],
                'contact_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => TRUE
                ],
                'avatar' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => TRUE
                ]
            ])
            ->add_key('profile_id', TRUE)
            ->add_key('user_id', FALSE, TRUE)
            ->create_table('student_profiles');

        // Foreign key: student_profiles.user_id -> users.user_id
        $this->_lava->db->query("
            ALTER TABLE `student_profiles`
            ADD CONSTRAINT `fk_student_profiles_user`
            FOREIGN KEY (`user_id`)
            REFERENCES `users` (`user_id`)
            ON UPDATE CASCADE
            ON DELETE CASCADE
        ");
    }

    public function down()
    {
        $this->_lava->dbforge->drop_table('student_profiles', TRUE);
    }
}
