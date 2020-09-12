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
            when{
                expression{ return(env.BRANCH_NAME == 'dev') }
            }
            environment{
                scannerHome = tool 'SonarQube'
            }
            steps{
                withSonarQubeEnv('SonarQube_6.0'){
                    sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=${projectKey} \
                    -Dsonar.projecName=${projectName} \
                    -Dsonar.projectVersion=1.0 \
                    -Dsonar.sources=./"
                    sh "/var/lib/jenkins/workspace/waitForSonarQube.sh"
                }
            }
        }
    }
    post{
        success{
            cleanWS()
        }
        failure{
            echo "========pipeline execution failed========"
        }
    }
}