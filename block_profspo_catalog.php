<?php

class block_profspo_catalog extends block_base
{
    public function init()
    {
        $this->title = get_string('profspo_catalog', 'block_profspo_catalog');
    }

    public function get_content()
    {
        global $CFG;
        if ($this->content !== null) {
            return $this->content;
        }

        $style = file_get_contents($CFG->dirroot . "/blocks/profspo_catalog/style/profspo_catalog.css");
        $js = file_get_contents($CFG->dirroot . "/blocks/profspo_catalog/js/profspo_catalog.js");
        $mainPage = file_get_contents($CFG->dirroot . "/blocks/profspo_catalog/templates/rendermainpage.mustache");

        $this->content = new stdClass;
        $this->content->text .= "<style>" . $style . "</style>";
        $this->content->text .= "<script src=\"https://code.jquery.com/jquery-1.9.1.min.js\"></script>";
        $this->content->text .= $mainPage;
        $this->content->text .= "<script type=\"text/javascript\"> " . $js . " </script>";


        return $this->content;
    }

    public function hide_header()
    {
        return true;
    }

    function has_config()
    {
        return true;
    }

}
