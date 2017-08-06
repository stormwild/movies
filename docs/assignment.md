# PHP Developer Exam
1. Set up a simple WordPress theme using https://roots.io/sage/
2. Install the Advanced Custom Field plugin
3. Set up and code the following:
1. Create a custom post type called “movies”
2. Create a nightly scheduled cron task to pull in all movies from http://
www.rcksld.com/GetNowShowing.json and then save this to a new Movies
post using the following mapping from Api: Post
- name: post_title
- synopsis: post_content
- PosterUrl: featured image
- Director: custom field Director
- MainCast custom field Main Cast

3. After this create a homepage template with a ACF repeater field where I can

select any movie and then output this in a basic layout.
4. Please add some notes / documentation on your code
5. Once done, please zip the theme only and send back