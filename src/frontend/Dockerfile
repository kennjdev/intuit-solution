ARG NODE_IMAGE
FROM $NODE_IMAGE
COPY ./package.json ./package-lock.json /frontend_node_modules/
WORKDIR /frontend_node_modules
RUN npm i > /dev/null
RUN chown -R node:node /frontend_node_modules
COPY --chown=node:node . /frontend
WORKDIR /frontend/
USER node
