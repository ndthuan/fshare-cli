# Requirements
- Aria2. It's available in most of popular Linux distros and Homebrew. You can also follow manual installation guide from https://aria2.github.io/.
- Gearman. It's also available in most of popular Linux distros and Homebrew. You can also follow manual installation guide from http://gearman.org/.
- PHP 5.5 or higher.
- Composer. Installation instructions: https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx.

# Installation
- You can download a ZIP version from https://github.com/ndthuan/fshare-cli or use git to clone the repo: `git clone https://github.com/ndthuan/fshare-cli.git`.
- Run `composer install` to install dependencies.
- Launch Aria2 RPC server: https://aria2.github.io/manual/en/html/aria2c.html#rpc-options.
- Copy `config/config.ini.dist` to `config/config.ini` and change options.
- Run `./bin/fshare daemon` to start the daemon.
- To autostart the daemon on boot. Consider using crontab, init scripts, supervisord configs... whichever way you favor.

# Recommended Aria2 Options
Please have a look at https://aria2.github.io/manual/en/html/aria2c.html#synopsis, there are important options such as `-d, --dir=<DIR>`, `-j, --max-concurrent-downloads=<N>`, `-s, --split=<N>`, `-x, --max-connection-per-server=<NUM>`, `-l, --log=<LOG>`...

`-j, --max-concurrent-downloads=<N>` must be tuned up together with `daemon.workers` in `config/config.ini`.

For example, if you use Aria2 for other downloads as well, you need to set it higher than `daemon.workers`.
