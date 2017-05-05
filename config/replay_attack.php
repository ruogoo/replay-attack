<?php
/**
 * This file is part of ruogoo.
 *
 * Created by HyanCat.
 *
 * Copyright (C) HyanCat. All rights reserved.
 */

return [

    /**
     * A switch of replay attacks.
     * If false, it will not check any request is a replay attack or not.
     */
    'enabled' => env('REPLAY_ATTACK', false),

    /**
     * The expire time in seconds.
     */
    'expire'  => 120,
];
