<?php

/*
Copyright 2014 Derek - web.development.help@gmail.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

class WordPressFunctionProxy
{

    /**
     * @param $postId
     * @return mixed
     */
    function get_post_proxy($postId)
    {
        return get_post($postId);
    }

    /**
     * @param $postId
     * @return mixed
     */
    function get_permalink_proxy($postId)
    {
        return get_permalink($postId);
    }

    /**
     * @param $post_id
     * @param $key
     * @param $single
     * @return mixed
     */
    function get_post_meta_proxy($post_id, $key, $single)
    {
        return get_post_meta($post_id, $key, $single);
    }

    /**
     * @param $feedname
     * @param $function
     * @return mixed
     */
    function add_feed_proxy($feedname, $function)
    {
        return add_feed($feedname, $function);
    }

    /**
     * @param $hook
     * @param $function_to_add
     * @param $priority
     * @param $accepted_args
     * @return mixed
     */
    function add_action_proxy($hook, $function_to_add, $priority = null, $accepted_args = null)
    {
        return add_action($hook, $function_to_add, $priority, $accepted_args);
    }

} 