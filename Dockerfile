FROM wordpress:latest

RUN apt-get update
RUN apt-get install nano htop
RUN echo 'set linenumbers' > ~/.nanorc
RUN chsh -s /bin/bash root
