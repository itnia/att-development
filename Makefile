

export
DOCKER_CONTAINER_NAME=att-development

build-image:
	docker build -t $(DOCKER_CONTAINER_NAME) -f Dockerfile .
run-image:
	docker run -d -p 8800:80 -v .:/var/www/html --name $(DOCKER_CONTAINER_NAME) $(DOCKER_CONTAINER_NAME)
shell:
	docker exec -it $(DOCKER_CONTAINER_NAME) sh