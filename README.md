<p align="center"><img src="https://i.ibb.co/ckmrTb0/bt.png"></p>  
  
<h1 align="center">BetterBuilder</h1>
<h3 align="center">⚠️ This project is a FORK of <a href="https://github.com/CzechPMDevs/BuilderTools">BuilderTools</a> maintained and updated by the <b>BetterLite</b> team for PocketMine-MP API 5.0.0!</h3>

<br><br>  
<p align="center">
   ✔️ Advanced WorldEdit commands  
    <br>  
    ✔️ Using Sub Chunk Iterator to make block placing faster
    <br>  
    ✔️ Supports schematics
    <br>  
    ✔️ Minecraft: Java Edition maps world fixer  
    <br>  
    ✔️ Supports PocketMine-MP API 5.0.0
    <br><br>  
</p>  
  
## 👍 1.5.0 Update:  
- Full native support for **PocketMine-MP API 5.0.0**
- Support for modern block runtime and state registries (`RuntimeBlockStateRegistry` & `GlobalBlockStateHandlers`)
- Full support for 3D biomes
- Enhanced async task thread-safety compliant with `pmmpthread 6.0`
- Restructured package and namespace to `betterlite\betterbuilder`
- Modern Wand Axe handling with dedicated NBT tag data
- General performance boosts and bug fixes
  
## 💬 FAQ
- `Required extension Core has an incompatible version (7.* not >=8.1)`
    - Your server is using an outdated PHP version. Please update your PHP binaries to PHP 8.1 or higher (PHP 8.2 recommended).
  
## 🔧 How to install BetterBuilder?  
  
1) Place the **BetterBuilder** folder or `.phar` file into your server's **/plugins/** directory  
2) Restart the server  
  
## 🏠 BetterBuilder commands  
  
- All BetterBuilder commands start with `//` except for the `/commands` and `/betterbuilder` command.  
- In game, you can get the list of all commands using `//commands`  
  
<br>  
  
**Commands**  
  
| **Command**          | **Description**                                                                                                                                                                                                                                                                |
|----------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| **//commands**       | **Displays list of BetterBuilder commands** <br><br> Alias: `//commands`, `/betterbuilder` <br>Usage: `//commands <page: 1-4>`                                                                                                                                                      |
| **//biome**          | **Changes biome in selected area** <br><br> Usage: `//biome list` OR `//biome <biomeId>`                                                                                                                                                                                       |
| **//blockinfo**      | **Switch block info mode** <br><br>Usage: `//blockinfo`<br>Alias: `//bi`<br><br>In blockinfo mode you can inspect block information by touching it.                                                                                                                           |
| **//clearinventory** | **Clears inventory** <br><br>Usage: `//clearinventory`<br>Alias: `//ci`                                                                                                                                                                                                        |
| **//center**         | **Finds center of the selection** <br><br> Usage: `//center` <br><br> Bedrock will appear in the middle of the selection                                                                                                                                                       |
| **//copy**           | **Copy selected area into the clipboard** <br><br> Usage: `//copy` <br><br> Copied area can be placed again using `//paste`, merged with the environment using `//merge` or rotated using `//rotate`.                                                                        |
| **//cube**           | **Creates cube** <br><br> Usage: `//cube <id1:dmg1,id2,...> <radius>` <br><br> Creates a cube at your position.                                                                                                                                                                |
| **//cut**            | **Cuts out selected area** <br><br> Usage: `//cube <id1:dmg1,id2,...> <radius>` <br><br> The area is afterwards moved into the clipboard.                                                                                                                                      |
| **//cylinder**       | **Creates cylinder** <br><br> Usage: `//cube <id1:dmg1,id2,...> <radius>` <br><br> Creates a cylinder at your position.                                                                                                                                                        |
| **//draw**           | **Draws with blocks** <br><br>Usage: `//draw <cube or sphere or off> [brush: 1-6] [fall = false]` <br><br> Recommended for creating terrain and mountains. Draw mode is toggled using `//draw <cube or sphere>` and turned off using `//draw off`.                           |
| **//fill**           | **Fill selected area** <br><br> Aliases: `//set`, `//change` <br> Usage: `//fill <id1:dmg1,id2,...>` <br><br> First select an area using `//pos1`, `//pos2` or with `//wand`.                                                                                                 |
| **//fix**            | **Fixes blocks in worlds imported from Minecraft: Java Edition** <br><br> Usage: `//fix <world>`                                                                                                                                                                               |  
| **//flip**           | **Flips selection** <br><br>Usage `//flip x` OR `//flip y` OR `//flip z`<br><br>The argument represents the axis to flip the selection along.                                                                                                                                  |
| **//hcube**          | **Creates hollow cube** <br><br>Usage: `//hcube <id1:dmg1,id2,...> <radius>`<br><br>Creates a hollow cube at your position.                                                                                                                                                   |  
| **//hcylinder**      | **Creates hollow cylinder** <br><br>Usage: `//hcyl <id1:dmg1,id2,...> <radius>`<br><br>Creates a hollow cylinder at your position.                                                                                                                                            |
| **//hpyramid**       | **Creates hollow pyramid** <br><br>Usage: `//hpyramid <id1:dmg1,id2,...> <radius>`<br><br>Creates a hollow pyramid at your position.                                                                                                                                          |
| **//hsphere**        | **Creates hollow sphere** <br><br>Usage: `//hsphere <id1:dmg1,id2,...> <radius>`<br><br>Creates a hollow sphere at your position.                                                                                                                                             |
| **//id**             | **Displays id and state of item in hand** <br><br>Usage: `//id`                                                                                                                                                                                                                |
| **//merge**          | **Merge copied area** <br><br> Usage: `//merge`                                                                                                                                                                                                                                |
| **//move**           | **Move blocks in selection** <br><br>Usage: `//move <x> <y> <z>`<br><br>Moves blocks within the selected area.                                                                                                                                                                 |
| **//naturalize**     | **Replaces blocks in selected area with natural terrain (grass, dirt, stone)** <br><br>Usage: `//naturalize`                                                                                                                                                                   |
| **//outline**        | **Fills outer layers of the selected area** <br><br>Usage: `//outline <id1:dmg1,id2,...>`                                                                                                                                                                                       |
| **//paste**          | **Paste copied area** <br><br> Usage: `//paste`                                                                                                                                                                                                                                |
| **//pos1**           | **Select first position** <br><br> Aliases: `//1`, `//pos1` <br> Usage: `//pos1`                                                                                                                                                                                               |
| **//pos2**           | **Select second position** <br><br> Aliases: `//2`, `//pos2` <br> Usage: `//pos2`                                                                                                                                                                                              |
| **//pyramid**        | **Creates pyramid** <br><br>Usage: `//pyramid <id1:dmg1,id2,...> <radius>`<br><br>Creates a pyramid at your position.                                                                                                                                                         |
| **//redo**           | **Redo last BetterBuilder action** <br><br> Usage: `//redo`                                                                                                                                                                                                                    |
| **//replace**        | **Replace blocks in selected area** <br><br> Usage: `//replace <blocksToReplace: id1,id2> <blocks: id1:dmg1,id2,...>`                                                                                                                                                          |
| **//rotate**         | **Rotate copied area** <br><br>Usage: `//rotate <y> [x] [z]` <br><br> Rotates clipboard along axis in degrees. Example: `//rotate 90`                                                                                                                                         |
| **//schematic**      | **Manage schematics** <br><br>Usage: `//schem <reload OR load OR list OR paste> [filename]`                                                                                                                                                                                    |
| **//sphere**         | **Creates sphere** <br><br> Usage: `//sphere <id1:dmg1,id2,...> <radius>` <br><br> Creates a sphere at your position.                                                                                                                                                         |
| **//stack**          | **Stacks copied area** <br><br>Usage: `//stack <count> [side or up or down]`<br><br>Repeats and stacks blocks in line.                                                                                                                                                         |
| **//tree**           | **Spawns tree** <br><br> Usage: `//tree <tree OR list>` <br><br> Basic tree objects (`oak`, `spruce`, `jungle` and `birch`)                                                                                                                                                   |
| **//undo**           | **Reverts last BetterBuilder action** <br><br> Usage: `//undo`                                                                                                                                                                                                                 |
| **//wand**           | **Toggle wand tool** <br><br> Usage: `//wand` <br><br> First position is selected by breaking a block, second by touching it.                                                                                                                                                 |

<br>

## 🛠️ Other features:

### 📜 Schematics
- You can save your selection to a file and load it back anytime.
- BetterBuilder supports loading [MCEdit](https://minecraft.fandom.com/wiki/Schematic_file_format) and [MCStructure](https://minecraft.fandom.com/wiki/Structure_Block#Save) formats, as well as creating schematics.
- Schematics are loaded and saved asynchronously to prevent server latency.

#### Loading schematics:
1) Move the schematic file to `/plugin_data/BetterBuilder/schematics/` directory
2) Load schematic using `//schem load <schematic>`
3) Paste into the world using `//schem paste <schematic>`

#### Creating schematics:
1) Select two positions using `//pos1` & `//pos2` commands or with the wand axe.
2) Use `//schem create <schematicName>`
3) The file will be saved in `/plugin_data/BetterBuilder/schematics/schematicName.schematic`

### 🌎 Fixing Java Edition worlds
- BetterBuilder can fix block IDs in worlds generated by Minecraft: Java Edition.
- Supports [Anvil](https://minecraft.fandom.com/wiki/Anvil_file_format) world format.
- To fix a world use `//fix <worldName>`
- Process runs asynchronously without freezing the server.

## 📃 Permissions  

**BetterBuilder Permissions:**
  
| Permission                          | Command            | Operator Permissions required |
|-------------------------------------|--------------------|-------------------------------|
| buildertools.command.help           | `//commands`       | ✔️                            |
| buildertools.command.biome          | `//biome`          | ✔️                            |
| buildertools.command.blockinfo      | `//blockinfo`      | ✔️                            |  
| buildertools.command.clearinventory | `//clearinventory` | ✔️                            |  
| buildertools.command.copy           | `//copy`           | ✔️                            |  
| buildertools.command.cube           | `//cube`           | ✔️                            |
| buildertools.command.cut            | `//cut`            | ✔️                            |
| buildertools.command.cylinder       | `//cylinder`       | ✔️                            |
| buildertools.command.decoration     | `//decoration`     | ✔️                            |
| buildertools.command.draw           | `//draw`           | ✔️                            |  
| buildertools.command.fill           | `//fill`           | ✔️                            |  
| buildertools.command.fix            | `//fix`            | ✔️                            |
| buildertools.command.hcube          | `//hcube`          | ✔️                            |  
| buildertools.command.hcylinder      | `//hcylinder`      | ✔️                            |  
| buildertools.command.hpyramid       | `//hpyramid`       | ✔️                            |  
| buildertools.command.hsphere        | `//hsphere`        | ✔️                            |  
| buildertools.command.id             | `//id`             | ✔️                            |  
| buildertools.command.merge          | `//merge`          | ✔️                            |  
| buildertools.command.move           | `//move`           | ✔️                            |  
| buildertools.command.naturalize     | `//naturalize`     | ✔️                            |  
| buildertools.command.outline        | `//outline`        | ✔️                            |  
| buildertools.command.paste          | `//paste`          | ✔️                            |  
| buildertools.command.pos1           | `//pos1`           | ✔️                            |  
| buildertools.command.pos2           | `//pos2`           | ✔️                            |  
| buildertools.command.pyramid        | `//pyramid`        | ✔️                            |  
| buildertools.command.rotate         | `//rotate`         | ✔️                            |  
| buildertools.command.schematic      | `//schematic`      | ✔️                            |  
| buildertools.command.sphere         | `//sphere`         | ✔️                            |  
| buildertools.command.stack          | `//stack`          | ✔️                            |  
| buildertools.command.tree           | `//tree`           | ✔️                            |  
| buildertools.command.undo           | `//undo`           | ✔️                            |
| buildertools.command.walls          | `//walls`          | ✔️                            |
| buildertools.command.wand           | `//wand`           | ✔️                            |  
  
## 🔧 Configuration  
Default configuration:

```yaml  
# BetterBuilder configuration file
# Target BetterBuilder version: 1.5.0

# Do not change this line.
config-version: 1.5.0

# This is format which will be used for creating schematics
# Supported formats: 'mcedit', 'mcstructure', 'buildertools'
output-schematics-format: 'mcedit'

# Option for compressing clipboards. This will make the actions
# slower, but reduces RAM usage.
clipboard-compression: true

# Some shapes are generated with duplicate blocks. This problem causes
# some blocks are not reverted right when doing //undo. This option
# is for removing duplicates.
# Warning: This action takes around 98% time of the whole process!
remove-duplicate-blocks: true

# BetterBuilder saves player's clipboard, undo & redo stuff when player
# leaves server to disk. This cache should be cleaned after restart (to
# avoid unexpected bugs). This option is to disable removing those files.
clean-cache: true

# When player leaves the server, player's session is saved, even if player did not
# do any action with BetterBuilder. If you enable this, player's sessions will not
# be saved and when player joins the server again, his clipboard data will be lost
# This option is good when BetterBuilder is only used as api plugin on server with
# high amount of players.
discard-sessions: false

# PowerItems settings:

# When disabled, //wand command still works, but instead of wand axe is hand
# going to be the 'wand tool'
wand-axe:
  enabled: true
  name: "§r§fWand Axe\n§7§oBreak for first pos\n§7§oTouch for second pos"

blockinfo-stick:
  enabled: false
  name: "§r§fDebug Stick\n§7§oTouch block for info"
```

<br>
  
## 💰 Credits  
  
- **BetterLite Team** - Maintenance, porting to PocketMine-MP API 5.0.0, and continuous development of the **BetterBuilder** fork.
- **CzechPMDevs / VixikHD** - Original authors and creators of the BuilderTools plugin.  
- Icon made by [Freepik](http://www.freepik.com/ "Freepik") from [www.flaticon.com](https://www.flaticon.com/ "Flaticon") (licensed under [CC 3.0 BY](http://creativecommons.org/licenses/by/3.0/ "Creative Commons BY 3.0")).  
- Seabuild spawn built by CryptoKey.

<br>

## 💡 License  
  
```  
Copyright (C) 2018-2022 CzechPMDevs
Copyright (C) 2026 BetterLite Team

Licensed under the Apache License, Version 2.0 (the "License");    
you may not use this file except in compliance with the License.    
You may obtain a copy of the License at    
   
https://www.apache.org/licenses/LICENSE-2.0    
   
Unless required by applicable law or agreed to in writing, software    
distributed under the License is distributed on an "AS IS" BASIS,    
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.    
See the License for the specific language governing permissions and    
limitations under the License.  
```  
