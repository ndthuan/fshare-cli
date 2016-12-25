# fshare-cli
fshare-cli is an unofficial tool for downloading files from fshare.vn in background. It consists of 2 parts:

- A daemon which in turn, consists of a certain number (configurable) of workers. Those workers wait for file download requests (public URLs such as http://www.fshare.vn/file/SOMEFILE/) from a Gearman server. The workers will fetch generated download URLs from those public URLs and submit them to an Aria2 RPC server.
- A simple command to submit download requests to the daemon workers through a Gearman server. Similarly to the official tool, it can process both folder URLs and individual file URLs.

This CLI tool is best in case you have a headless home server (usually some Linux distro) and you want to download files from fshare.vn 24/7 without having to install a desktop and the graphical tool from FPT.

You can also test this tool on a Mac computer. You need to use Homebrew to install Aria2 and Gearman server.

# Installation
See [detailed installation guide](./INSTALL.md).

# Command Usage
- To submit a download request: `./bin/fshare download http://www.fshare.vn/folder/SOMEFOLDER/` or `./bin/fshare download http://www.fshare.vn/file/SOMEFILE/`. By default, Aria2 will save files to the directory you specify in the command that you use to launch Aria2 RPC server.
- To save files to a different directory: `./bin/fshare download [The Fshare URL] /path/to/your/folder`

# Limitation
- This tool still lacks of abilities to control the queue like to pause/resume, stop or remove a download...

# Probable Questions
#### Why do we need a queue server?
Aria2 RPC has its own work queue. You might wonder why we don't just fetch download URLs then let the Aria2 take care of the queue. Let's consider following factors:

- Since download URLs are generated when requested, we can't be sure how long they will last.
- Aria's number of connections per server is limited.
- Our internet connection speed is also limited. We can't simply download all files at the same time.

So our Gearman server will keep public URLs such as `http://www.fshare.vn/file/SOMEFILE/`. Only at the time it's processed, a download URL is generated and we're sure that it's downloadable.

#### Why Gearman but not a more advanced queue server like RabbitMQ?
RabbitMQ is too big for our simple needs. It requires more lines of code to do simple tasks as in our case.

# Legality
This tool simply simulates a web browser. It does not hack into Fshare servers. Neither it redistributes the files.

In a similar situation, there are tools to download videos from YouTube. Those tools are all legal.

# Disclaimer
**Use it at your own risks!** You are legally responsible for what you download, how you use downloaded files. The author and contributors of fshare-cli are not accountable for the violation (if any) to Fshare's terms of service which caused by fshare-cli's users.

# License
Source code of this tool is released under MIT License.
