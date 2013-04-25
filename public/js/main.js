/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(function(){
    $('#query').autocomplete({
        serviceUrl: $('#query').data('url'), // Страница для обработки запросов автозаполнения
        minChars: 2, // Минимальная длина запроса для срабатывания автозаполнения
        maxHeight: 400, // Максимальная высота списка подсказок, в пикселях
        width: $('#query').innerWidth(), // Ширина списка
        zIndex: 9999, // z-index списка
        deferRequestBy: 0 // Задержка запроса (мсек), на случай, если мы не хотим слать миллион запросов, пока пользователь печатает. Я обычно ставлю 300.
    });

});