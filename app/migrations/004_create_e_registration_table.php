
<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Create_e_registration_table
{
    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        if ($this->_lava->dbforge->table_exists('event_registrations')) {
            return;
        }

        $this->_lava->dbforge
            ->add_field([
                'event_reg_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                    'null'           => FALSE
                ],
                'event_id' => [
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
                'qr_token' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => FALSE
                ],
                'registered_at' => [
                    'type'    => 'TIMESTAMP',
                    'null'    => FALSE,
                    'default' => 'CURRENT_TIMESTAMP'
                ]
            ])
            ->add_key('event_reg_id', TRUE)
            ->add_key('qr_token', FALSE, TRUE)
            ->add_key(['event_id', 'user_id'], FALSE, TRUE)
            ->add_key('event_id')
            ->add_key('user_id')
            ->create_table('event_registrations');

        // Foreign key: event_registrations.event_id -> events.event_id
        $this->_lava->db->query("
            ALTER TABLE `event_registrations`
            ADD CONSTRAINT `fk_event_registrations_event`
            FOREIGN KEY (`event_id`)
            REFERENCES `events` (`event_id`)
            ON UPDATE CASCADE
            ON DELETE CASCADE
        ");

        // Foreign key: event_registrations.user_id -> users.user_id
        $this->_lava->db->query("
            ALTER TABLE `event_registrations`
            ADD CONSTRAINT `fk_event_registrations_user`
            FOREIGN KEY (`user_id`)
            REFERENCES `users` (`user_id`)
            ON UPDATE CASCADE
            ON DELETE CASCADE
        ");
    }

    public function down()
    {
        $this->_lava->dbforge->drop_table('event_registrations', TRUE);
    }
}
