
<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Create_org_registration_table
{
    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        if ($this->_lava->dbforge->table_exists('org_registrations')) {
            return;
        }

        $this->_lava->dbforge
            ->add_field([
                'reg_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                    'null'           => FALSE
                ],
                'org_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => TRUE,
                    'null'       => FALSE
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => TRUE,
                    'null'       => FALSE
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => "'pending','approved','rejected'",
                    'null'       => FALSE,
                    'default'    => 'pending'
                ],
                'registered_at' => [
                    'type'    => 'TIMESTAMP',
                    'null'    => FALSE,
                    'default' => 'CURRENT_TIMESTAMP'
                ]
            ])
            ->add_key('reg_id', TRUE)
            ->add_key(['org_id', 'user_id'], FALSE, TRUE)
            ->add_key('org_id')
            ->add_key('user_id')
            ->create_table('org_registrations');

        // FK: org_registrations.org_id -> organizations.org_id
        $this->_lava->db->query("
            ALTER TABLE `org_registrations`
            ADD CONSTRAINT `fk_org_registrations_org`
            FOREIGN KEY (`org_id`)
            REFERENCES `organizations` (`org_id`)
            ON UPDATE CASCADE
            ON DELETE CASCADE
        ");

        // FK: org_registrations.user_id -> users.user_id
        $this->_lava->db->query("
            ALTER TABLE `org_registrations`
            ADD CONSTRAINT `fk_org_registrations_user`
            FOREIGN KEY (`user_id`)
            REFERENCES `users` (`user_id`)
            ON UPDATE CASCADE
            ON DELETE CASCADE
        ");
    }

    public function down()
    {
        $this->_lava->dbforge->drop_table('org_registrations', TRUE);
    }
}
