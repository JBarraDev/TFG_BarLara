<?php
function e($string): string {
	return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function formatPrice($price): string {
	return number_format($price, 2, ',', '.') . ' €';
}

function formatDate($date, $format = 'd/m/Y H:i'): string {
	return (new DateTime($date))->format($format);
}

