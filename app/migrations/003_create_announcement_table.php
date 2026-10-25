
<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Create_announcement_table {

    private $_lava;
    protected $dbforge;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        if ($this->_lava->dbforge->table_exists('announcements')) {
            return;
        }

        $this->_lava->dbforge->add_field([
            'announcement_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE,
                'null'           => FALSE
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => FALSE
            ],
            'body' => [
                'type' => 'TEXT',
                'null' => FALSE
            ],
            'posted_by' => [
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
        ]);

        $this->_lava->dbforge->add_key('announcement_id', TRUE);
        $this->_lava->dbforge->add_key('posted_by');

        $this->_lava->dbforge->create_table('announcements');

        // Foreign key: announcements.posted_by -> users.user_id
        $this->_lava->db->query("
            ALTER TABLE `announcements`
            ADD CONSTRAINT `fk_announcements_posted_by`
            FOREIGN KEY (`posted_by`)
            REFERENCES `users` (`user_id`)
            ON UPDATE CASCADE
            ON DELETE SET NULL
        ");
    }

    public function down()
    {
        $this->_lava->dbforge->drop_table('announcements', TRUE);
    }
}
