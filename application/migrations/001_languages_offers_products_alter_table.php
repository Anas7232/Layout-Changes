<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Migration_languages_offers_products_alter_table extends CI_Migration
{
    public function up()
    {
        /* adding new fields in offers table */
        $fields = array(
            'type' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => TRUE,
				'after' => 'id'
			),
            'type_id' => array(
				'type' => 'INT',
				'constraint' => '11',
				'default' => '0',
				'after' => 'type'
			),
        );
        $this->dbforge->add_column('offers', $fields);
		/* adding new fields in order_items table */
        $fields = array(
            'product_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '512',
				'null' => TRUE,
				'after' => 'order_id'
			),
            'variant_name' => array(
				'type' => 'VARCHAR',
				'constraint' => '256',
				'after' => 'product_name',
                'null'=>TRUE,
			),
        );
        $this->dbforge->add_column('order_items', $fields);

		/* adding new fields in products table */
        $fields = array(
            'video_type' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => TRUE,
				'after' => 'other_images'
			),
            'video' => array(
				'type' => 'VARCHAR',
				'constraint' => '512',
				'null' => TRUE,
				'after' => 'video_type'
			),
            'tags' => array(
				'type' => 'TEXT',
				'null' => TRUE,
				'after' => 'video'
			),
            'warranty_period' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => TRUE,
				'after' => 'tags'
			),
            'guarantee_period' => array(
				'type' => 'VARCHAR',
				'constraint' => '32',
				'null' => TRUE,
				'after' => 'warranty_period'
			),
        );
        $this->dbforge->add_column('products', $fields);
		
		/* adding new table languages */
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => TRUE
            ],
            'language' => [
                'type'           => 'VARCHAR',
                'constraint'     => '128',
                'NULL'     => true,
            ],
            'code' => [
                'type'           => 'VARCHAR',
                'constraint'     => '8',
                'NULL'     => TRUE,
            ],
            'is_rtl' => [
                'type'           => 'TINYINT',
                'constraint'     => '4',
                'DEFAULT'     => '0',
            ],
            'created_on TIMESTAMP default CURRENT_TIMESTAMP',
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('languages');

        /* dumping the data into the languages table */
        $data = [
            [
                'language' => "English",
                'code' => "en",
                'is_rtl' => "0"
            ]
        ];
        $this->db->insert_batch('languages', $data);

        /* adding new table themes */
        $this->dbforge->add_field([
            'id' => [
                'type'           => 'INT',
                'constraint'     => '11',
                'auto_increment' => TRUE
            ],
            'name' => [
                'type'           => 'VARCHAR',
                'constraint'     => '32',
            ],
            'slug' => [
                'type'           => 'VARCHAR',
                'constraint'     => '32',
            ],
            'image' => [
                'type'           => 'VARCHAR',
                'constraint'     => '512',
                'NULL'           => TRUE
            ],
            'is_default' => [
                'type'           => 'TINYINT',
                'constraint'     => '4',
                'DEFAULT'     => '0',
            ],
            'status' => [
                'type'           => 'TINYINT',
                'constraint'     => '4',
                'DEFAULT'     => '0',
            ],
            'created_on TIMESTAMP default CURRENT_TIMESTAMP',
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('themes');

        /* dumping the data into the languages table */
        $data = [
            [
                'name' => "Classic",
                'slug' => "classic",
                'image' => "classic.jpg",
                'is_default' => "1",
                'status'=>"1"
            ]
        ];
        $this->db->insert_batch('themes', $data);
    }

    public function down()
    {
        // Drop table 
        $this->dbforge->drop_table('languages');
        $this->dbforge->drop_table('themes');
        // Drop columns >> $this->dbforge->drop_column('table_name', 'column_to_drop');

        $this->dbforge->drop_column('offers', 'type');
        $this->dbforge->drop_column('offers', 'type_id');
        $this->dbforge->drop_column('products', 'video_type');
        $this->dbforge->drop_column('products', 'video');
        $this->dbforge->drop_column('products', 'tags');
        $this->dbforge->drop_column('products', 'warranty_period');
        $this->dbforge->drop_column('order_items', 'product_name');
        $this->dbforge->drop_column('order_items', 'variant_name');
    }
}