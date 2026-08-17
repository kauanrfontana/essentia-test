<?php

test('the root url redirects to the customers list', function () {
    $this->get('/')->assertRedirect('/customers');
});
