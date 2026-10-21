# TIDONG Virtual Life — V4 Changes

## Added in V4
- Interactive 3D Furniture Studio linked to V2 demo inventory stored on the same browser origin/device.
- An owned home item can be displayed in 3D, put away, or reordered. Placement is limited to eight displayed items; purchased inventory is not consumed by moving it.
- Three no-reward character activity actions: Relax, Read, Tend Plants. These reposition a basic 3D character; they are **not** cinematic motion-capture animation.
- Four 3D avatar colors and Day/Sunset/Night presets, saved back to the existing offline demo if game entry has occurred.
- Responsive mobile controls and 3D fallback for devices without WebGL.
- New tests for inventory ownership, multiple placements, state corruption and layout reordering.

## Preserved from earlier versions
- V2 2.5D home, market, simulated friend home and park.
- 18 demo virtual products; 10 sample tasks; demo welcome balance of 25,000 promotional T-Coins and game access cost of 199 demo T-Coins.
- No financial payouts, real INR, customer profile integration or real item ownership.

## Review boundaries
- Browser localStorage is editable by the person controlling the device; it is never suitable as authoritative inventory or a financial ledger.
- Offline single-player demo only. Friend visits are fictional; real multiplayer, messaging and invitations remain unbuilt.
- Browser UI tested with software 3D fallback; native hardware WebGL has not been verified in this environment.
- Original project databases, orders, Laravel source, GitHub and live deployment were not changed.
