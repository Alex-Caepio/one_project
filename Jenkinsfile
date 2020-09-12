pipeline{
    environment{
        projectName = "oneness_back"
        projectKey = "oneness_back:1"
        workingEnv = "dev"
        registryHost = "https://nexus-docker.andersenlab.dev"
        registryName = "nexus-docker.andersenlab.dev"
        registryCredentials = "nexus_andersen"
        dockerCredentials = "oneness_docker_credentials"
    }
    agent{
        label "master"
    }
    stages{
        stage("Sonarqube"){
            environment{
                scannerHome = tool 'SonarQube'
            }
            steps{
                withSonarQubeEnv('SonarQube_6.0'){
                    sh "${scannerHome}/bin/sonar-scanner \
                    -Dsonar.projectKey=${projectKey} \
                    -Dsonar.projectName=${projectName} \
                    -Dsonar.projectVersion=1.0 \
                    -Dsonar.sources=./"
                    sh "/var/lib/jenkins/workspace/waitForSonarQube.sh"
                }
            }
        }
        stage("Build docker image"){
            steps{
                configFileProvider([configFile(fileId: 'oneness_envconfig_dev', targetLocation: '.env')]){
                    script{
                        sh "ls -lah ${WORKSPACE}"
                        dockerImage = docker.build('$projectName:$workingEnv', "-f ./Dockerfile ./")
                    }
                }
            }
        }
        stage("Deploy docker image to Nexus"){
            steps{
                script{
                    docker.withRegistry(registryHost,registryCredentials){
                        dockerImage.push()
                    }
                }
            }
        }
        stage("Clean from docker images"){
            steps{
                sh '''docker rmi -f $(docker images --filter=reference=${projectName} -q) >/dev/null 2>&1'''
            }
        }
    }
    post{
        success{
            cleanWs()
        }
        failure{
            echo "========pipeline execution failed========"
        }
    }
}