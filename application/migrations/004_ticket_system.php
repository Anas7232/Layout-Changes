<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_ticket_system extends CI_Migration
{
    // and I think user_type_id ni jagya a simply user_id rakhi daia to chalse
    public function up()
    {
        /* adding new table ticket_types */
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => TRUE
            ],
            'title' => [
                'type' => 'TEXT',
                'NULL'     => true,
            ],
            'date_created TIMESTAMP default CURRENT_TIMESTAMP',
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('ticket_types');

        /* adding new table tickets */
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => TRUE
            ],
            'ticket_type_id' => [
                'type'           => 'INT',
                'constraint'     => '11'
            ],
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => '11'
            ],
            'subject' => [
                'type' => 'TEXT',
                'NULL'     => true,
            ],
            'email' => [
                'type' => 'TEXT',
                'NULL'     => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'NULL'     => true,
            ],
            'status' => [
                'type'           => 'TINYINT',
                'constraint'     => '4',
                'DEFAULT'     => '0',
            ],
            'last_updated TIMESTAMP on update CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'date_created TIMESTAMP default CURRENT_TIMESTAMP',
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('tickets');

        /* adding new table ticket_messages */
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => TRUE
            ],
            'user_type' => [
                'type' => 'TEXT',
                'NULL'     => true,
            ],
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => '11'
            ],
            'ticket_id' => [
                'type'           => 'INT',
                'constraint'     => '11'
            ],
            'message' => [
                'type' => 'TEXT',
                'NULL'     => true,
            ],
            'attachments' => [
                'type'           => 'VARCHAR',
                'constraint'     => '512',
                'NULL'           => TRUE
            ],
            'last_updated TIMESTAMP on update CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'date_created TIMESTAMP default CURRENT_TIMESTAMP',
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('ticket_messages');

        /* adding new fields in products table */
        $fields = array(
            'swatche_type' => array(
                'type' => 'INT',
                'constraint' => '11',
                'DEFAULT'    => '0',
                'after' => 'value'
            ),
            'swatche_value' => array(
                'type' => 'VARCHAR',
				'constraint' => '512',
				'null' => TRUE,
                'after' => 'swatche_type'
            )
        );
        $this->dbforge->add_column('attribute_values', $fields);
    }

    public function down()
    {
        // Drop table 
        // Drop columns >> $this->dbforge->drop_column('table_name', 'column_to_drop');
        $this->dbforge->drop_column('attribute_values', 'swatche_type');
        $this->dbforge->drop_column('attribute_values', 'swatche_value');
        $this->dbforge->drop_table('ticket_messages');
        $this->dbforge->drop_table('tickets');
        $this->dbforge->drop_table('ticket_types');
    }
}
