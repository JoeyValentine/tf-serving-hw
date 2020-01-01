1. Build docker image from Dockerfile  
<code>docker build -t IMAGE_NAME --build-arg model_url=MODEL_URL .</code>  
You can get COCO trained MODEL_URLs from :  
https://github.com/tensorflow/models/blob/master/research/object_detection/g3doc/detection_model_zoo.md  

2. Run docker container from object-detect docker image  
<code>docker run -p 8080:8080 -p 8081:8081 --name CONTAINER_NAME -it IMAGE_NAME</code>
