pipeline{
	agent any

	stages {
		stage('compose install'){
			steps{
				sh 'composer install'
			}
		}
		
		stage('test'){
			steps{
				sh 'php artisan test'
			}
		}

		stage('static analysis'){
			steps{
				sh '/vendor/bin/phpstan analyse'
			}
		}

		stage('code style'){
			steps{
				sh '/vendor/bin/pint --test'
			}			
		}
	}
}
