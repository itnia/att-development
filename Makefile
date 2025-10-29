
build-image:
	docker build -t att-development -f Dockerfile .
run-image:
	docker run -d -p 8800:80 -v .:/var/www/html --name att-development att-development
