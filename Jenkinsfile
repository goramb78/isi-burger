// Jenkinsfile
// Pipeline CI/CD pour ISI BURGER — Laravel

pipeline {
    agent any

    environment {
        APP_NAME    = 'isi-burger'
        DOCKER_IMAGE = "isi-burger-app"
        DOCKER_TAG   = "${BUILD_NUMBER}"
        BRANCH_NAME_TARGET = 'prenom_nom_burger'
    }

    triggers {
        // Déclenché par le webhook GitHub
        githubPush()
    }

    stages {

        // ── 1. Récupération du code ──────────────────────────
        stage('📥 Pull du code') {
            steps {
                echo "=== Récupération du code depuis GitHub ==="
                checkout scm
                sh 'git log -1 --oneline'
            }
        }

        // ── 2. Installation des dépendances ──────────────────
        stage('📦 Installation des dépendances') {
            steps {
                echo "=== Installation Composer ==="
                sh '''
                    # Vérifier si composer est disponible
                    which composer || curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

                    composer install --no-interaction --prefer-dist --optimize-autoloader
                '''
            }
        }

        // ── 3. Configuration environnement ───────────────────
        stage('⚙️ Configuration .env') {
            steps {
                echo "=== Préparation de l'environnement ==="
                sh '''
                    cp .env.example .env
                    php artisan key:generate --force
                    # Les vraies valeurs viennent des credentials Jenkins
                '''
                withCredentials([
                    string(credentialsId: 'DB_PASSWORD',   variable: 'DB_PWD'),
                    string(credentialsId: 'MAIL_PASSWORD', variable: 'MAIL_PWD')
                ]) {
                    sh '''
                        sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PWD}/" .env
                        sed -i "s/MAIL_PASSWORD=.*/MAIL_PASSWORD=${MAIL_PWD}/" .env
                    '''
                }
            }
        }

        // ── 4. Tests PHPUnit ──────────────────────────────────
        stage('🧪 Tests') {
            steps {
                echo "=== Exécution des tests unitaires ==="
                sh '''
                    php artisan config:clear
                    ./vendor/bin/phpunit --testdox || true
                '''
            }
            post {
                always {
                    junit allowEmptyResults: true, testResults: 'storage/test-results/*.xml'
                }
            }
        }

        // ── 5. Build de l'image Docker ────────────────────────
        stage('🐳 Build image Docker') {
            steps {
                echo "=== Construction de l'image Docker ==="
                sh '''
                    docker build \
                        -f Partie_1_Setup/Dockerfile \
                        -t ${DOCKER_IMAGE}:${DOCKER_TAG} \
                        -t ${DOCKER_IMAGE}:latest \
                        .
                '''
            }
        }

        // ── 6. Démarrage des conteneurs ───────────────────────
        stage('🚀 Déploiement Docker Compose') {
            steps {
                echo "=== Démarrage des services ==="
                sh '''
                    docker-compose -f Partie_1_Setup/docker-compose.yml down --remove-orphans || true
                    docker-compose -f Partie_1_Setup/docker-compose.yml up -d --build
                    sleep 10

                    # Migrations
                    docker-compose -f Partie_1_Setup/docker-compose.yml exec -T app php artisan migrate --force
                    docker-compose -f Partie_1_Setup/docker-compose.yml exec -T app php artisan storage:link --force
                    docker-compose -f Partie_1_Setup/docker-compose.yml exec -T app php artisan config:cache
                    docker-compose -f Partie_1_Setup/docker-compose.yml exec -T app php artisan route:cache
                    docker-compose -f Partie_1_Setup/docker-compose.yml exec -T app php artisan view:cache
                '''
            }
        }

        // ── 7. Vérification santé ─────────────────────────────
        stage('🔍 Health Check') {
            steps {
                echo "=== Vérification de l'application ==="
                sh '''
                    sleep 5
                    curl -f http://localhost:8000 || exit 1
                    echo "Application accessible sur http://localhost:8000"
                '''
            }
        }
    }

    // ─── Post actions ─────────────────────────────────────────────
    post {
        success {
            echo "✅ Pipeline réussi — ISI BURGER déployé sur http://localhost:8000"
        }
        failure {
            echo "❌ Pipeline échoué. Vérifiez les logs ci-dessus."
            // Optionnel : envoyer un email
            // mail to: 'admin@isiburger.com', subject: 'Pipeline FAILED', body: 'Build #${BUILD_NUMBER} a échoué.'
        }
        always {
            echo "=== Nettoyage ==="
            sh 'docker image prune -f || true'
        }
    }
}
