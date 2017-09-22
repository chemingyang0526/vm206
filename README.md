This branch, HT-Responsive, contains all the responsive changes.

Two diff files have been added to the repo. diff file lists only the file names (the changes found using diff --brief command) and diff-full lists the changes including file names (found using diff command). 

in root folder we have all files and folders, that we have in /usr/share/htvcenter after installation. But we have them without 'logs' folder and 'storage'. Because 'logs' we don't need to save and 'storage' is NFS folder.

Also we have new one folder - 'install': in this folder all files from install script,but without 'buildtmp' folder.

'buildtmp' folder is temp folder, so we don't need it, but this folder can save downloading time in installation process. (We can take the folder from any previous versions of install script, because I never did changes there). This folder cannot be uploaded to github, because some files there have a big size.
