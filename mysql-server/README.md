1. Set environment variables in Dockerfile
2. <code>docker create volume VOLUME_NAME</code>
3. <code>docker build -t IMAGE_NAME .</code>
4. <code>docker run -d -p 3306:3306 --name CONTAINER_NAME -v VOLUME_NAME:/var/lib/mysql IMAGE_NAME</code>
