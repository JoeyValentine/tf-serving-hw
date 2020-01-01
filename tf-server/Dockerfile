FROM tensorflow/serving

RUN apt-get update
RUN apt-get install -qy wget

ARG model_url

WORKDIR /models
RUN wget -nv -O model.tar.gz $model_url
RUN tar -xvf model.tar.gz
RUN mkdir -p object-detect/1
RUN find -name saved_model -exec mv {}/saved_model.pb {}/variables object-detect/1/ \;

EXPOSE 8080
ENTRYPOINT ["tensorflow_model_server", "--model_base_path=/models/object-detect"]
CMD ["--rest_api_port=8080","--port=8081"]