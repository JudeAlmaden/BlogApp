<?php
function sanitizeOutput($data): string {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
