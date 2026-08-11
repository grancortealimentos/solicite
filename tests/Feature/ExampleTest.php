<?php

test('unauthenticated users are redirected to login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
