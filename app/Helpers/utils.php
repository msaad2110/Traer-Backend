<?php
function wt_get_public_file_view_path($path)
{
    return url('/attachments') . '?path=' . urlencode($path);
}
