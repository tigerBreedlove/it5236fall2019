#!/user/bin/env bash
git pull && docker build --no-cache -t doit-app . && docker kill doit-app-container && docker system prune -f && docker run -d --name doit-app-container -p 80:80 doit-app
