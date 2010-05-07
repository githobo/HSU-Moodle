Using the geogebra filter makes it a lot easier to embed geogebra worksheets into  
moodle online documents.

How it works:
During installation of the filter the file geogebra.jar will be placed in the moodle central folder and registered at the moddle system.
Teachers will be able to embed previously uploaded *.ggb (Geogebra) files into a moodle online document simply by creating a link to the ggb file using (as usual) the link symbol in the editor bar. 
As an option you will be able to customize width and height of the applet. When saving the document, the link will be automatically converted in HTML-Code, which will display the applet instead of the link.    

Installation: (by Moodle Admin)
1. Upload the complete folder "geogebra" into the folder  moodle-->filter
2. In Moodle, navigate to Moodle->Administration->Configuration->"Filter" and click on the entry
   "geogebra" to activate the filter

Usage:
Usage:
1. In a Moodle course: -> Add a resource ->compose a website 
2. Write content. At the position the applet should appear, create a link to the (previously 
     uploaded) *.ggb file. 
          a Write some link text
          b Select the link text.
          c Click the „chain icon “ in the tool bar of the editor.
3. In the appearing small Window choose your .ggb file. (Change folder, if necessary.) 
4 Optionally: At the end of the link text type values for width and height of the applet 
   according to the following  pattern:
             myfile.ggbwidth=600height=300 (Default values 400x400)
   
5 Close the window

Be aware of the fact, that you dont't see the applet unless you leave the editor and save your document.
On reopening it later, you will notice the link rather than the applet.  


A restriction to be eliminated soon:
Placing more than one ggb in one document will give alle of them the same dimensions.
 