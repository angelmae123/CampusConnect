
<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Create_sections_table
{
    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        if ($this->_lava->dbforge->table_exists('sections')) {
            return;
        }

        $this->_lava->dbforge
            ->add_field([
                'section_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                    'null'           => FALSE
                ],
                'dept_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => TRUE,
                    'null'       => FALSE
                ],
                'yr_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => TRUE,
                    'null'       => FALSE
                ],
                'section_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => FALSE
                ]
            ])
            ->add_key('section_id', TRUE)
            ->add_key(['dept_id', 'yr_id', 'section_name'], FALSE, TRUE)
            ->add_key('dept_id')
            ->add_key('yr_id')
            ->create_table('sections');

        // Foreign key: sections.dept_id -> departments.dept_id
        $this->_lava->db->query("
            ALTER TABLE `sections`
            ADD CONSTRAINT `fk_sections_department`
            FOREIGN KEY (`dept_id`)
            REFERENCES `departments` (`dept_id`)
            ON UPDATE CASCADE
            ON DELETE RESTRICT
        ");

        // Foreign key: sections.yr_id -> yr_levels.yr_id
        $this->_lava->db->query("
            ALTER TABLE `sections`
            ADD CONSTRAINT `fk_sections_year_level`
            FOREIGN KEY (`yr_id`)
            REFERENCES `yr_levels` (`yr_id`)
            ON UPDATE CASCADE
            ON DELETE RESTRICT
        ");
    }

    public function down()
    {
        $this->_lava->dbforge->drop_table('sections', TRUE);
    }
}
