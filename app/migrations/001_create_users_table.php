
<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Create_users_table
{
    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        // Skip if the table already exists.
        if ($this->_lava->dbforge->table_exists('users')) {
            return;
        }

        $this->_lava->dbforge
            ->add_field([
                'user_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                    'null'           => FALSE,
                ],
                'student_no' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => TRUE,
                ],
                'full_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => FALSE,
                ],
                'email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => FALSE,
                ],
                'password_hash' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => FALSE,
                ],
                'role' => [
                    'type'       => 'ENUM',
                    'constraint' => "'student','admin'",
                    'null'       => FALSE,
                    'default'    => 'student',
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => "'active','inactive'",
                    'null'       => FALSE,
                    'default'    => 'active',
                ],
                'section_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => TRUE,
                    'null'       => TRUE,
                ],
                'yr_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => TRUE,
                    'null'       => TRUE,
                ],
                'created_at' => [
                    'type'    => 'TIMESTAMP',
                    'null'    => FALSE,
                    'default' => 'CURRENT_TIMESTAMP',
                ],
            ])
            ->add_key('user_id', TRUE)
            ->add_key('student_no', FALSE, TRUE)
            ->add_key('email', FALSE, TRUE)
            ->add_key('section_id')
            ->add_key('yr_id')
            ->create_table('users');

        // Foreign key: users.section_id -> sections.section_id
        $this->_lava->db->query("
            ALTER TABLE `users`
            ADD CONSTRAINT `fk_users_section`
            FOREIGN KEY (`section_id`)
            REFERENCES `sections` (`section_id`)
            ON UPDATE CASCADE
            ON DELETE SET NULL
        ");

        // Foreign key: users.yr_id -> yr_levels.yr_id
        $this->_lava->db->query("
            ALTER TABLE `users`
            ADD CONSTRAINT `fk_users_yr_level`
            FOREIGN KEY (`yr_id`)
            REFERENCES `yr_levels` (`yr_id`)
            ON UPDATE CASCADE
            ON DELETE SET NULL
        ");
    }

    public function down()
    {
        $this->_lava->dbforge->drop_table('users', TRUE);
    }
}
