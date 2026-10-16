<!-- File Path: resources/views/member/game/partials/player-character.blade.php -->
<script>
    // Player State and Customization logic
    const playerState = {
        x: 0,
        z: 0,
        speed: 0.5,
        outfitColor: 0x38bdf8
    };

    function movePlayer(direction) {
        if (!player) return;
        if (direction === 'forward') player.position.z -= playerState.speed;
        if (direction === 'backward') player.position.z += playerState.speed;
        if (direction === 'left') player.position.x -= playerState.speed;
        if (direction === 'right') player.position.x += playerState.speed;

        // Smooth Camera Follow
        camera.position.x = player.position.x;
        camera.position.z = player.position.z + 14;
        camera.lookAt(player.position);
    }

    // Change character outfit color when items bought from market
    function updatePlayerOutfit(colorHex) {
        if (player && player.material) {
            player.material.color.setHex(colorHex);
        }
    }

    // Keyboard Listeners
    window.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowUp' || e.key === 'w') movePlayer('forward');
        if (e.key === 'ArrowDown' || e.key === 's') movePlayer('backward');
        if (e.key === 'ArrowLeft' || e.key === 'a') movePlayer('left');
        if (e.key === 'ArrowRight' || e.key === 'd') movePlayer('right');
    });
</script>