div.row {
    clear: both;
    padding-top: 15px;
    margin-left: 10px;
}

div.row span.label {
    float: left;
    width: 40%;
}

div.row span.formw {
    float: right;
    width: 60%;
}

div.row span.heading {
    float: left;
    width: 40%;
    font-weight: bold;
    padding-top: 5px;
    padding-bottom: 5px;
    padding-left: 5px;
}

div.row span.button {
    padding-top: 10px;
    float: left;
    width: 60%;
}

div.centerblock {
	margin: 50px 0px; padding:0px;
	text-align: center;
}

#blockcontent {
	width: 500px;
	margin: 0px auto;
	text-align: left;
	padding: 15px;
	border: 1px dashed #333;
	background-color: #eee;
}

.block_course_menu ul {
    list-style: none;
    margin: 0px;
    padding: 10px;
}

.block_course_menu img {
    vertical-align: top;
}


<?php
    //styles for the module nodes in YUI
    $mods = get_records('modules', '', '', 'name');
    foreach ($mods as $mod) {
        if ($mod->name != 'resource' && $mod->name != 'label') {
            $css = '.icon-'.substr($mod->name,0,4).' { padding-left:22px; width:20px; height:20px; background: url('
            . $CFG->modpixpath.'/'.$mod->name.'/icon.gif) 0 0 no-repeat;) }';
            echo $css . "\n";
        }
    }
?>
.icon-wor {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/word.gif) 0 0 no-repeat;
}
.icon-aud {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/audio.gif) 0 0 no-repeat;
}
.icon-avi {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/avi.gif) 0 0 no-repeat;
}
.icon-dmg {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/dmg.gif) 0 0 no-repeat;
}
.icon-exc {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/excel.gif) 0 0 no-repeat;
}
.icon-fla {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/flash.gif) 0 0 no-repeat;
}
.icon-fol {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/folder.gif) 0 0 no-repeat;
}
.icon-htm {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/html.gif) 0 0 no-repeat;
}
.icon-imar {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/image.gif) 0 0 no-repeat;
}
.icon-jbc {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/jbc.gif) 0 0 no-repeat;
}
.icon-jcl {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/jcl.gif) 0 0 no-repeat;
}
.icon-jcw {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/jcw.gif) 0 0 no-repeat;
}
.icon-jmt {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/jmt.gif) 0 0 no-repeat;
}
.icon-jmx {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/jmx.gif) 0 0 no-repeat;
}
.icon-jqz {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/jqz.gif) 0 0 no-repeat;
}
.icon-odb {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/odb.gif) 0 0 no-repeat;
}
.icon-odc {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/odc.gif) 0 0 no-repeat;
}
.icon-odf {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/odf.gif) 0 0 no-repeat;
}
.icon-odg {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/odg.gif) 0 0 no-repeat;
}
.icon-odi {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/odi.gif) 0 0 no-repeat;
}
.icon-odm {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/odm.gif) 0 0 no-repeat;
}
.icon-odp {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/odp.gif) 0 0 no-repeat;
}
.icon-ods {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/ods.gif) 0 0 no-repeat;
}
.icon-odt {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/odt.gif) 0 0 no-repeat;
}
.icon-pdf {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/pdf.gif) 0 0 no-repeat;
}
.icon-pow {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/powerpoint.gif) 0 0 no-repeat;
}
.icon-wor {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/word.gif) 0 0 no-repeat;
}
.icon-tex {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/text.gif) 0 0 no-repeat;
}
.icon-vid {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/video.gif) 0 0 no-repeat;
}
.icon-web {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/web.gif) 0 0 no-repeat;
}
.icon-xml {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/xml.gif) 0 0 no-repeat;
}
.icon-zip {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->pixpath;?>/f/zip.gif) 0 0 no-repeat;
}
.icon-oth {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->modpixpath;?>/resource/icon.gif) 0 0 no-repeat;
}
.icon-cfg {
    padding-left:22px;
    width:20px; height:20px; 
    background: url(<?php echo $CFG->wwwroot;?>/blocks/course_menu/icons/configure.gif) 0 0 no-repeat;
}
.icon-grd {
    padding-left: 22px;
    width: 20px; height:20px;
    background: url(<?php echo $CFG->pixpath;?>/i/grades.gif) 0 0 no-repeat;  
}

.icon-lb {
    padding-left: 22px;
    width: 20px; height:20px;
    background: url(<?php echo $CFG->wwwroot;?>/blocks/course_menu/icons/book_open.png) 0 0 no-repeat;
}

.icon-tkt {
    padding-left: 22px;
    width: 20px; height:20px;
    background: url(<?php echo $CFG->wwwroot;?>/blocks/course_menu/icons/bug.gif) 0 0 no-repeat;  
}

.icon-ppl {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->pixpath;?>/i/users.gif) 0 0 no-repeat;
}

.icon-cal {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->wwwroot;?>/blocks/course_menu/icons/cal.gif) 0 0 no-repeat;
}

.icon-fld-emp {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->wwwroot;?>/blocks/course_menu/icons/file.gif) 0 0 no-repeat;
}
.icon-fld {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->wwwroot;?>/blocks/course_menu/icons/folder.gif) 0 0 no-repeat;
}
.icon-shw {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->wwwroot;?>/blocks/course_menu/icons/viewall.gif) 0 0 no-repeat;
}

.icon-crs {
    font-size: 12pt;
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->wwwroot;?>/blocks/course_menu/icons/openfolder.gif) 0 0 no-repeat;
}

.icon-edt {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->pixpath;?>/i/settings.gif) 0 0 no-repeat;
}

.icon-eml {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->pixpath;?>/i/email.gif) 0 0 no-repeat;
}

.icon-fls {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->pixpath;?>/i/files.gif) 0 0 no-repeat;
}

.icon-psw {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->wwwroot;?>/blocks/course_menu/icons/userpwd.gif) 0 0 no-repeat;
}

.icon-rep {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->pixpath;?>/i/stats.gif) 0 0 no-repeat;
}

.icon-usr {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->pixpath;?>/i/users.gif) 0 0 no-repeat;
}

.icon-grp {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->pixpath;?>/i/group.gif) 0 0 no-repeat;
}

.icon-bkp {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->pixpath;?>/i/backup.gif) 0 0 no-repeat;
}

.icon-rst {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->pixpath;?>/i/restore.gif) 0 0 no-repeat;
}

.icon-scl {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->pixpath;?>/i/scales.gif) 0 0 no-repeat;
}

.icon-pfl {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->pixpath;?>/i/guest.gif) 0 0 no-repeat;
}

.icon-rls {
    padding-left: 22px;
    width: 20px; height: 20px;
    background: url(<?php echo $CFG->pixpath;?>/i/roles.gif) 0 0 no-repeat;
}

/* Copyright (c) 2006 Yahoo! Inc. All rights reserved. */

/* first or middle sibling, no children */
.ygtvtn {
    width:16px; height:22px; 
    background: url(<?php echo $CFG->wwwroot;?>/lib/yui/treeview/assets/tn.gif) 0 0 no-repeat; 
}

/* first or middle sibling, collapsable */
.ygtvtm {
    width:16px; height:22px; 
    cursor:pointer ;
    background: url(<?php echo $CFG->wwwroot;?>/lib/yui/treeview/assets/tm.gif) 0 0 no-repeat; 
}

/* first or middle sibling, collapsable, hover */
.ygtvtmh {
    width:16px; height:22px;  
    cursor:pointer ;
    background: url(<?php echo $CFG->wwwroot;?>/lib/yui/treeview/assets/tmh.gif) 0 0 no-repeat; 
}

/* first or middle sibling, expandable */
.ygtvtp {
    width:16px; height:22px; 
    cursor:pointer ;
    background: url(<?php echo $CFG->wwwroot;?>/lib/yui/treeview/assets/tp.gif) 0 0 no-repeat; 
}

/* first or middle sibling, expandable, hover */
.ygtvtph {
    width:16px; height:22px; 
    cursor:pointer ;
    background: url(<?php echo $CFG->wwwroot;?>/lib/yui/treeview/assets/tph.gif) 0 0 no-repeat; 
}

/* last sibling, no children */
.ygtvln {
    width:16px; height:22px; 
    background: url(<?php echo $CFG->wwwroot;?>/lib/yui/treeview/assets/ln.gif) 0 0 no-repeat; 
}

/* Last sibling, collapsable */
.ygtvlm {
    width:16px; height:22px; 
    cursor:pointer ;
    background: url(<?php echo $CFG->wwwroot;?>/lib/yui/treeview/assets/lm.gif) 0 0 no-repeat; 
}

/* Last sibling, collapsable, hover */
.ygtvlmh {
    width:16px; height:22px; 
    cursor:pointer ;
    background: url(<?php echo $CFG->wwwroot;?>/lib/yui/treeview/assets/lmh.gif) 0 0 no-repeat; 
}

/* Last sibling, expandable */
.ygtvlp { 
    width:16px; height:22px; 
    cursor:pointer ;
    background: url(<?php echo $CFG->wwwroot;?>/lib/yui/treeview/assets/lp.gif) 0 0 no-repeat; 
}

/* Last sibling, expandable, hover */
.ygtvlph { 
    width:16px; height:22px; cursor:pointer ;
    background: url(<?php echo $CFG->wwwroot;?>/lib/yui/treeview/assets/lph.gif) 0 0 no-repeat; 
}

/* Loading icon */
.ygtvloading { 
    width:16px; height:22px; 
    background: url(<?php echo $CFG->wwwroot;?>/lib/yui/treeview/assets/loading.gif) 0 0 no-repeat; 
}

/* the style for the empty cells that are used for rendering the depth 
 * of the node */
.ygtvdepthcell { 
    width:16px; height:22px; 
    background: url(<?php echo $CFG->wwwroot;?>/lib/yui/treeview/assets/vline.gif) 0 0 no-repeat; 
}

.ygtvblankdepthcell { width:16px; height:22px; }

/* the style of the div around each node */
.ygtvitem { }  

/* the style of the div around each node's collection of children */
.ygtvchildren { }  
* html .ygtvchildren { height:2%; }  

/* the style of the text label in ygTextNode */
.ygtvlabel, .ygtvlabel:link, .ygtvlabel:visited, .ygtvlabel:hover { 
    margin-left:2px;
    text-decoration: none;
}

.ygtvspacer { height: 10px; width: 10px; margin: 2px; }
