/* These are styles for discussion_subscription */


.forumheaderlist .subscribed { 
  text-align: left;
  white-space: nowrap;
  width: 35px;
}

.forumtopicnav { 
  margin: 5px;
}

#mod-forum-view .forumsortorder {
  float: left;
  clear: left;
}

.forumactions {
  margin-top: 5px;
  float: left;
  clear: left;
}

.forumprevtopic { 
  font-weight: bold; 
  float:left;
}

.forumnexttopic { 
  font-weight: bold; 
  text-align:right;
}

#mod-forum-discuss .forumcontrol .subscription {
  float: right;
  text-align:right;
  white-space: nowrap;
}


/*The following styles are for the expandable/collapsable forum functionality.
Styles beginning with #treeDiv are for the javascript version and
styles beginning with .forumheaderlist are for the non-javascript version.
Some display inconsistencies with alignment may occur with the javascript version
due to the fact that the YUI builds each expandable row as its own table.*/

#treeDiv2 .picture {
    width: 35px;
}

#treeDiv2 .replies {
  text-align: center;
  white-space: nowrap;
  width: 12%;
}

#treeDiv2 .topic {
  vertical-align: middle;
  white-space: wrap;
  background: #DDDDDD none repeat scroll 0%;
}

#treeDiv2 .topic.five {
  width: 24%;
}

#treeDiv2 .topic.six {
  width: 20%;
}

#treeDiv2 .topic.seven {
  width: 18%;
}

.forumheaderlist .starter {
  vertical-align: middle;
  background: #DDDDDD none repeat scroll 0%;
}

#treeDiv2 table {
   width: 100%;
   border-collapse: seperate;
}

#treeDiv2 td {
  border-width:1px 0px 0px 1px;
  border-style:solid;
  border-color:#FFFFFF;
}

#treeDiv2 .author {
  //white-space: nowrap;
  width: 19%;
}

#treeDiv2 .group {
  //white-space: nowrap;
  width: 12%;
  text-align: center;
  font-size: 0.6em;
}

#treeDiv2 .lastpost {
  white-space: nowrap;
  text-align: right;
  font-size: 0.6em;
  width: 23%
}

#treeDiv2 .content {
  padding: 4px;
  background: #FFFFFF none repeat scroll 0%;
  min-width: 30%;
  float: left;
  clear: both;
}

.forumheaderlist .content {
  padding: 4px;
  background: #FFFFFF none repeat scroll 0%;
  min-width: 30%;
  float: left;
  clear: both;
}

#treeDiv2 .indent {
  margin-left: 30px;
  float: left;
  clear: both;
}

#treeDiv2 .subscribed {
  width: 18%;
  text-align: center;
}

.forumheaderlist .subscribed {
  text-align: center;
}

#treeDiv2 .content .commands {
  clear: both;
  padding-top: 0.05em;
  text-align: right;
}

.forumheaderlist .content .commands {
  clear: both;
  padding-top: 0.05em;
  text-align: right;
}

.forumexport {
  margin: 20px;
  text-align: center;
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
