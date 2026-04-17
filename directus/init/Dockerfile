FROM node:20-alpine
WORKDIR /init
COPY package.json ./
RUN npm install --omit=dev
COPY init.mjs ./
CMD ["node", "init.mjs"]
