<?php
class block_shelfari_widget extends block_base {

    function init(){
        $this->title = get_string('title','block_shelfari_widget');
        $this->version = 2010031600;
    }

    function get_content() {
        if ($this->content != NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        if(empty($this->config->shelfarinum)) {
            $this->content->text = get_string('noconfig','block_shelfari_widget');
        }
        else {
            $this->content->text = '
                <div id="ShelfariWidget' . $this->config->shelfarinum . '">
                <a href="http://www.shelfari.com/">Shelfari: Book reviews on your book blog</a>
                <script src="http://www.shelfari.com/ws/' . $this->config->shelfarinum . '/widget.js" type="text/javascript" language="javascript">
                </script><noscript>
                <p>Find new <a href="http://www.shelfari.com/books">books</a>
                and literate friends with Shelfari, the online
                <a href="http://www.shelfari.com/">book club</a>.</p></noscript></div>';
        }
        $this->content->footer = '';
        return $this->content;
    }

    function instance_allow_config() {
        return true;
    }
}

?>
