<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tidong Real 3D World</title>
    <style>
        body { margin: 0; overflow: hidden; font-family: sans-serif; background: #87ceeb; }
        #hud {
            position: absolute; top: 15px; left: 15px;
            background: rgba(0,0,0,0.75); color: white;
            padding: 12px 20px; border-radius: 8px; backdrop-filter: blur(5px);
            z-index: 10;
        }
        .controls {
            position: absolute; bottom: 20px; left: 20px;
            display: grid; grid-template-columns: repeat(3, 50px); gap: 5px;
            z-index: 10;
        }
        .btn {
            width: 50px; height: 50px; background: rgba(255,255,255,0.3);
            border: none; color: white; font-size: 20px; border-radius: 8px;
            cursor: pointer;
        }
        .btn:active { background: rgba(255,255,255,0.6); }
    </style>
</head>
<body>

    <!-- Blade Partials Inclusion -->
    @include('game.partials.hud')
    @include('game.partials.controls')
    @include('game.partials.game-script')

</body>
</html>