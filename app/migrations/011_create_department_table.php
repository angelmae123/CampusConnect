
<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Create_department_table
{
    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        if ($this->_lava->dbforge->table_exists('departments')) {
            return;
        }

        $this->_lava->dbforge
            ->add_field([
                'dept_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                    'null'           => FALSE
                ],
                'dept_code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => FALSE
                ],
                'dept_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => FALSE
                ]
            ])
            ->add_key('dept_id', TRUE)
            ->add_key('dept_code', FALSE, TRUE)
            ->add_key('dept_name', FALSE, TRUE)
            ->create_table('departments');
    }

    public function down()
    {
        $this->_lava->dbforge->drop_table('departments', TRUE);
    }
}
