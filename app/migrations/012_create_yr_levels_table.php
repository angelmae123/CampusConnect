
<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Create_yr_levels_table
{
    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        if ($this->_lava->dbforge->table_exists('yr_levels')) {
            return;
        }

        $this->_lava->dbforge
            ->add_field([
                'yr_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                    'null'           => FALSE
                ],
                'yr_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => FALSE
                ]
            ])
            ->add_key('yr_id', TRUE)
            ->add_key('yr_name', FALSE, TRUE)
            ->create_table('yr_levels');
    }

    public function down()
    {
        $this->_lava->dbforge->drop_table('yr_levels', TRUE);
    }
}
