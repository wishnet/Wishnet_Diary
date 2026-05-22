<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 心情映射
 */
function mood_emoji($mood)
{
    $map = array(
        '开心' => '😊',
        '难过' => '😢',
        '平静' => '😐',
        '兴奋' => '😆',
        '焦虑' => '😰',
        '感恩' => '🙏',
        '其他' => '🤔',
    );
    return isset($map[$mood]) ? $map[$mood] : '📝';
}

function mood_options()
{
    return array('开心', '难过', '平静', '兴奋', '焦虑', '感恩', '其他');
}

/**
 * 天气映射
 */
function weather_emoji($weather)
{
    $map = array(
        '晴' => '☀️',
        '多云' => '⛅',
        '阴' => '☁️',
        '雨' => '🌧️',
        '雪' => '❄️',
        '风' => '🌬️',
        '其他' => '🤷',
    );
    return isset($map[$weather]) ? $map[$weather] : '🌤️';
}

function weather_options()
{
    return array('晴', '多云', '阴', '雨', '雪', '风', '其他');
}

/**
 * 格式化日期为中文显示
 */
function format_date_cn($date_str)
{
    $timestamp = strtotime($date_str);
    return date('Y年n月j日', $timestamp);
}

/**
 * 获取日期中的月日部分
 */
function format_date_md($date_str)
{
    $timestamp = strtotime($date_str);
    return date('n月j日', $timestamp);
}

/**
 * 获取日期的年月
 */
function format_date_ym($date_str)
{
    $timestamp = strtotime($date_str);
    return date('Y年n月', $timestamp);
}

/**
 * 截取文本摘要
 */
function text_excerpt($text, $length = 200)
{
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . '...';
}

/**
 * 解析标签字符串为数组
 */
function parse_tags($tags_str)
{
    if (empty($tags_str)) {
        return array();
    }
    $tags = explode(',', $tags_str);
    $tags = array_map('trim', $tags);
    $tags = array_filter($tags);
    return array_values($tags);
}
