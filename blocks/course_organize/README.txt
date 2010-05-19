07/01/2009
Moodle Block: Course Organize

This block was designed so users would have the ability to control the layout of 
their courses on the My Moodle home page.  It was developed in response to users
having some courses with higher activity then others and being automatically 
placed at the bottom of the page, requiring the user to scroll through the other
courses first.

INSTRUCTIONS
-------------
For instructions on how to install a contributed block please visit:
http://docs.moodle.org/en/Installing_contributed_modules_or_plugins

The course_organize block was initially developed to be available to 
people who need or requested it so the admin will have to 
select who has permission to use it. The admin user will see a block
with a link to manage roles or users and will have the option to search for
and select users to add or to make the block available to everyone
by setting the sitewide option or to specific roles.  The 'Populate'
option below the roles selection adds everyone with the selected role
while retaining the ability to manage individual users. When the user 
is added, a block will appear on their My Moodle page with a link to 
Organize Courses. Once there, the user will be able to organize their 
courses.

The block requires small core code modification. If you are not
comfortable modifying core code or have a policy that prohibits
it then you should not install this block. Below are the 
instructions for the modification.

In the function print_my_moodle() in the file $CFG->wwwroot/course/lib.php:

if (!empty($courses) || !empty($rcourses) || !empty($rhosts)) {

        if (!empty($courses)) {

        	//New course organize code
			if(record_exists('block_course_organize', 'userid', $USER->id)) {
				if($courseOrder = unserialize(base64_decode(get_field('block_course_organize', 'classorder', 'userid', $USER->id)))) {
					$newOrder = array();
					if(count($courseOrder) != count($courses)) {
						foreach($courses as $course) {
							if(!in_array($course->id, $courseOrder)) {
								array_push($newOrder, $course);
							}
						}
					}
					for($i = 0; $i < count($courseOrder); $i++) {
						if($courses[$courseOrder[$i]]) {
							array_push($newOrder, $courses[$courseOrder[$i]]);
						}
					}
					$courses = $newOrder;
					unset($newOrder);
				} 
			}
        	//End course organize code

            echo '<ul class="unlist">';
            foreach ($courses as $course) {

If you have any question, comments or suggestions please send them to Dean Mitchell dean.m81@gmail.com

TODO: Add ability to organize into categories.

NOTE: This block was designed to be minimalist. It could have used
      AJAX or YUI but I opted for a simpler approach.

