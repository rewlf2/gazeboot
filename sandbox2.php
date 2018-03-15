<?php

// Credits to Chris Shiflett's article hosted on http://shiflett.org/articles/storing-sessions-in-a-database
// This pile of script is a renewed version of session storing functions, to properly fit with newer php scripts 
session_set_save_handler('_open',
                         '_close',
                         '_read',
                         '_write',
                         '_destroy',
                         '_clean');

?>