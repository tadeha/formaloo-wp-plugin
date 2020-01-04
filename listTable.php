<?php
/**
 * Create a new table class that will extend the WP_List_Table
 */
class Forms_List_Table extends WP_List_Table {

    private $formData = array();

    public function setFormData($formData) { 
        $this->formData = $formData; 
    }
    public function getFormData() { 
        return $this->formData; 
    }

    private $privateKey = '';

    public function setPrivateKey($privateKey) { 
        $this->privateKey = $privateKey; 
    }
    public function getPrivateKey() { 
        return $this->privateKey; 
    }

    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->table_data();
        usort( $data, array( &$this, 'sort_data' ) );

        $formData = $this->getFormData();

        $perPage = $formData['data']['page_size'];
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            // 'cb'            => '<input type="checkbox" />',
            'title'         => __('Title', 'formaloo'),
            'active'        => __('Active', 'formaloo'),
            'submitCount'   => __('Submit Count', 'formaloo'),
            'excel'         => __('Download Results', 'formaloo')
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array('title' => array('title', false), 'submitCount' => array('submitCount', false));
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data() {
        $tableData = array();
        $data = $this->getFormData();
        $index = 0;
        $modalTitle = __('Set-up Form Settings', 'formaloo');

        foreach($data['data']['forms'] as $form) {
            $tableData[] = array(
                'ID'           => $index,
                'title'        => '<a href="#TB_inline?&width=100vh&height=100vw&inlineId=form-show-options" class="thickbox" title="'. $modalTitle .'" onclick = "getRowInfo(\''. $form['slug'] .'\',\''. $form['address'] .'\')"><strong class="formaloo-table-title">'. $form['title'] .'</strong></a>',
                'active'       => ($form['active']) ? '<span class="dashicons dashicons-yes success-message"></span>' : '<span class="dashicons dashicons-no-alt error-message"></span>',
                'submitCount'  => $form['submit_count'],
                'slug'         => $form['slug'],
                'address'      => $form['address'],
                'excel'        => '<button class="button formaloo-get-excel-link" data-form-slug="'. $form['slug'] .'"> <span class="dashicons dashicons-download"></span> Download </button></form>'
            );
            $index++;
        }

        return $tableData;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            // case 'cb':
            case 'title':
            case 'active':
            case 'submitCount':
            case 'excel':
                return $item[ $column_name ];

            default:
                return print_r( $item, true ) ;
        }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b ) {

        // Set defaults
        $orderby = 'title';
        $order = 'asc';

        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }

        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }

        $result = strnatcmp( $a[$orderby], $b[$orderby] );

        if($order === 'asc')
        {
            return $result;
        }

        return -$result;
    }

    function column_title($item) {
        $actions = array(
                  'view'      => sprintf('<a href="%s://%s/%s" target="_blank">View Form</a>',FORMALOO_PROTOCOL,FORMALOO_ENDPOINT,$item['address']),
                  'edit'      => '<a href="#TB_inline?&width=100vh&height=100vw&inlineId=form-show-edit" class="thickbox" title="Edit Form" onclick = "showEditFormWith(\''. FORMALOO_PROTOCOL .'\', \''. FORMALOO_ENDPOINT .'\', \''. $item['slug'] .'\')">Edit Form</a>'
                  //  sprintf('<a href="%s://%s/dashboard/my-forms/%s/edit" target="_blank">Edit Form</a>',FORMALOO_PROTOCOL,FORMALOO_ENDPOINT,$item['slug']),
                  // 'delete'    => sprintf('<a href="?page=%s&action=%s&book=%s">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
              );
      
        return sprintf('%1$s %2$s', $item['title'], $this->row_actions($actions) );
    }

    
}