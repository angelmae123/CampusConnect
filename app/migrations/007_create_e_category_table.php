
<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Create_e_category_table
{
    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        if ($this->_lava->dbforge->table_exists('event_categories')) {
            return;
        }

        $this->_lava->dbforge
            ->add_field([
                'category_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                    'null'           => FALSE
                ],
                'category_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => FALSE
                ]
            ])
            ->add_key('category_id', TRUE)
            ->add_key('category_name', FALSE, TRUE)
            ->create_table('event_categories');
    }

    public function down()
    {
        $this->_lava->dbforge->drop_table('event_categories', TRUE);
    }
}
