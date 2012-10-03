<?php
if (!defined('IN_CMS')) {
	exit();
}
?>


<p>
	This plugin helps you to edit multiple 
	pages based on <strong>jQuery</strong>, 
	so you don't have to wait for page reload and 
	click <strong>"Save and continue editing"</strong> 
	every 5 seconds. 
	All changes are made (almost) instantly.
</p>
<p>
	It's especially useful for SEO purposes 
	(like optimizing meta descriptions and titles) 
	or quick editing large number of pages.
</p><br/><br/>
<hr/>
<h3>MultiEdit in frontend </h3>
<p>
  To include MultiEdit in <b>frontend</b>, make sure you have <b>jQuery (1.4.2+)</b>
available in frontend (layout) for example like this:
</p>
<pre style="background-color: #EEE; border: 1px solid black;">
 &lt;script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.js"/&gt;
</pre>
<p>
Then put this code somewhere at the 
end of your layout code (ideally - just <b>before &lt;/body&gt; ending</b> tag):
<pre style="background-color: #EEE; border: 1px solid black;">
  &lt;?php 
    if ( Plugin::isEnabled('multiedit') &&
         AuthUser::hasRole('administrator') ) {
           getMultiEdit($this->id);
        } 
  ?&gt;
</pre>
This way users with role <b>Administrator</b> will be able to edit page metadata
in frontend
</p>
<br/>
<hr/>
<p>Wolf CMS repository: <a href="http://www.wolfcms.org/repository/120">http://www.wolfcms.org/repository/120</a><br>
	Git repository: <a href="https://github.com/marekmurawski/multiedit">https://github.com/marekmurawski/multiedit</a>
</p>
