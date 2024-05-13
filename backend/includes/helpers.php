<?php
function sanitize_input($data)
{
    return htmlspecialchars(trim($data));
}