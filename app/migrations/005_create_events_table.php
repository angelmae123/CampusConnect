
<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Create_events_table
{
    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        if ($this->_lava->dbforge->table_exists('events')) {
            return;
        }

        $this->_lava->dbforge
            ->add_field([
                'event_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                    'null'           => FALSE
                ],
                'category_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => TRUE,
                    'null'       => FALSE
                ],
                'title' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => FALSE
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => FALSE
                ],
                'venue' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => FALSE
                ],
                'image' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => TRUE
                ],
                'start_datetime' => [
                    'type' => 'DATETIME',
                    'null' => FALSE
                ],
                'end_datetime' => [
                    'type' => 'DATETIME',
                    'null' => FALSE
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => "'upcoming','ongoing','completed','cancelled'",
                    'null'       => FALSE,
                    'default'    => 'upcoming'
                ],
                'created_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => TRUE,
                    'null'       => TRUE
                ],
                'created_at' => [
                    'type'    => 'TIMESTAMP',
                    'null'    => FALSE,
                    'default' => 'CURRENT_TIMESTAMP'
                ]
            ])
            ->add_key('event_id', TRUE)
            ->add_key('category_id')
            ->add_key('created_by')
            ->create_table('events');

        // Foreign key: events.category_id -> event_categories.category_id
        $this->_lava->db->query("
            ALTER TABLE `events`
            ADD CONSTRAINT `fk_events_category`
            FOREIGN KEY (`category_id`)
            REFERENCES `event_categories` (`category_id`)
            ON UPDATE CASCADE
            ON DELETE RESTRICT
        ");

        // Foreign key: events.created_by -> users.user_id
        $this->_lava->db->query("
            ALTER TABLE `events`
            ADD CONSTRAINT `fk_events_creator`
            FOREIGN KEY (`created_by`)
            REFERENCES `users` (`user_id`)
            ON UPDATE CASCADE
            ON DELETE SET NULL
        ");
    }

    public function down()
    {
        $this->_lava->dbforge->drop_table('events', TRUE);
    }
}
