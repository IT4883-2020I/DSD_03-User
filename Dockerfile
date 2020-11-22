FROM node:12.18-alpine
WORKDIR /app
COPY . .
RUN npm install
CMD ["npm", "run", "serve"]