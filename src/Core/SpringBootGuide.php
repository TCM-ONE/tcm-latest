<?php

declare(strict_types=1);

namespace TCM\Core;

/** A practical beginner-to-advanced Spring Boot curriculum for Java learners. */
final class SpringBootGuide
{
    /** @return array<string,array<string,mixed>> */
    public static function all(): array
    {
        return [
            'spring-boot' => [
                'name' => 'Spring Boot',
                'category' => 'Java backend framework',
                'version' => 'Spring Boot 4.1.2 · stable',
                'versionNote' => 'Spring Boot 4.1.2 is the latest stable release checked on 24 October 2026. Spring Boot 4.2.0-RC1 is a preview, so this guide uses the stable line. Boot 4.1 requires Java 17 or later and supports Java up to 27. Confirm build-tool, library and hosting compatibility before starting a real project.',
                'level' => 'Beginner to advanced · Java foundations recommended',
                'duration' => '14 modules · learn at your own pace',
                'icon' => 'bi-cup-hot',
                'summary' => 'Learn Spring Boot with Java: create a backend, build REST APIs, validate data, connect a database, write tests and prepare a secure application for deployment.',
                'prerequisites' => 'Start with the TCM Java guide first if classes, methods, collections, exceptions and Maven or Gradle are new. You need a JDK 17 or newer, a code editor and an internet connection to create the starter project. Java 25 is an LTS option; use the JDK supported by your libraries and deployment platform.',
                'outcome' => 'Create a maintainable Java REST API with clear controller, service and repository roles; persist data, validate requests, test behavior, configure environments and package an executable application.',
                'courseCategory' => 'Programming Foundations',
                'lessons' => [
                    [
                        'title' => '1. What Spring Boot does and how it relates to Java',
                        'explanation' => 'Java is the language; Spring Framework provides tools for connecting application objects and handling web requests; Spring Boot adds sensible defaults, dependency starters and an embedded server so you can start a Java backend with less setup. Boot does not replace Java fundamentals. It organizes them into an application that can start on its own, usually with java -jar. First learn what a request, response, class, method and exception are, then Spring annotations will feel like readable instructions instead of magic.',
                        'example' => "Java source code\n  ↓ compile\nJava bytecode + Spring libraries\n  ↓ SpringApplication.run(...)\nEmbedded web server listens for HTTP requests\n  ↓ controller handles a request\nJSON response returned to the caller",
                        'practice' => 'In your own words, explain the difference between Java, Spring Framework and Spring Boot. Name one job an embedded server does.',
                    ],
                    [
                        'title' => '2. Create a project with Spring Initializr',
                        'explanation' => 'Spring Initializr at start.spring.io generates the build files and folder structure for you. Choose Maven or Gradle, Java, a stable Spring Boot 4.1.x version, a clear group and artifact name, and the Spring Web dependency. In Boot 4 the MVC starter is spring-boot-starter-webmvc. Generate and unzip the project, then open the folder containing pom.xml or build.gradle. Use an IDE that imports Maven or Gradle so it can download dependencies and show compile errors.',
                        'example' => "Project choices\nProject: Maven\nLanguage: Java\nSpring Boot: latest stable 4.1.x\nGroup: in.tcm.learning\nArtifact: study-api\nDependency: Spring Web (MVC)\n\n# Confirm the installed JDK\njava -version",
                        'practice' => 'Generate a starter project named study-api and identify the build file, main source folder and test folder.',
                    ],
                    [
                        'title' => '3. Read the project structure and build file',
                        'explanation' => 'The build file declares the Spring Boot version and dependencies. Maven uses pom.xml; Gradle uses build.gradle or build.gradle.kts. The build tool downloads the libraries, compiles Java and packages the app. Keep your main application class in a root package such as in.tcm.learning so component scanning can find subpackages like controller and service. Do not manually copy random jar files into the project; declare dependencies in the build file so they can be reproduced on another computer.',
                        'example' => <<<'CODE'
study-api/
├── pom.xml
└── src/
    ├── main/java/in/tcm/learning/
    │   └── StudyApiApplication.java
    ├── main/resources/
    │   └── application.properties
    └── test/java/in/tcm/learning/
        └── StudyApiApplicationTests.java
CODE,
                        'practice' => 'Find the Boot parent or plugin and the web MVC starter in your generated build file. Explain why src/main/java and src/test/java are separate.',
                    ],
                    [
                        'title' => '4. Start the application and understand auto-configuration',
                        'explanation' => '@SpringBootApplication combines configuration, auto-configuration and component scanning. Auto-configuration looks at the libraries you included and prepares common infrastructure when suitable configuration is missing. For example, the MVC starter gives Boot enough information to configure a web application and embedded Tomcat. It is a starting default, not a rule you can never change. The main method calls SpringApplication.run, which builds the application context and starts the server.',
                        'example' => <<<'CODE'
package in.tcm.learning;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;

@SpringBootApplication
public class StudyApiApplication {
    public static void main(String[] args) {
        SpringApplication.run(StudyApiApplication.class, args);
    }
}
CODE,
                        'practice' => 'Run the generated project with ./mvnw spring-boot:run (Windows: mvnw.cmd spring-boot:run) and locate the startup message that shows the server port.',
                    ],
                    [
                        'title' => '5. Build your first REST endpoint',
                        'explanation' => 'A REST endpoint is an HTTP method and path that your application understands. @RestController marks a class as a web handler whose return values are written to the response body. @GetMapping maps a GET request to a method. A GET should retrieve information and should not secretly change server data. With a JSON library on the classpath, returning a Java object can produce JSON; returning plain text produces a simple text response.',
                        'example' => <<<'CODE'
package in.tcm.learning.controller;

import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
public class HelloController {
    @GetMapping("/api/hello")
    public String hello() {
        return "Hello from Spring Boot";
    }
}
CODE,
                        'practice' => 'Add GET /api/hello, run the app and open http://localhost:8080/api/hello. Note the difference between the URL path and the Java method name.',
                    ],
                    [
                        'title' => '6. Request mappings, path variables and query parameters',
                        'explanation' => 'HTTP methods communicate the kind of action: GET reads, POST creates, PUT replaces, PATCH partially changes, and DELETE removes. A path variable identifies a resource in the route, such as /api/lessons/7. A query parameter adds optional filtering or search information, such as ?level=beginner. Use specific mapping annotations so the intended method is clear, and choose resource-oriented paths that remain readable as the API grows.',
                        'example' => <<<'CODE'
@RestController
@RequestMapping("/api/lessons")
public class LessonController {
    @GetMapping("/{id}")
    public String byId(@PathVariable long id) {
        return "Lesson " + id;
    }

    @GetMapping
    public String byLevel(@RequestParam(defaultValue = "all") String level) {
        return "Level: " + level;
    }
}
CODE,
                        'practice' => 'Try /api/lessons/7 and /api/lessons?level=beginner. Then add a second query parameter for a search term.',
                    ],
                    [
                        'title' => '7. DTOs, JSON and request validation',
                        'explanation' => 'A DTO (data transfer object) is a simple shape for information that crosses the API boundary. It keeps the public request format separate from internal database entities. A record is useful for an immutable request or response shape. Bean Validation annotations describe rules; @Valid asks Spring to apply those rules to an incoming request. In Boot 4, add the validation starter from Initializr or your build file. Invalid input should produce a useful 400 response, and server-side checks are essential even if a browser form also validates.',
                        'example' => <<<'CODE'
import jakarta.validation.constraints.NotBlank;

public record CreateLessonRequest(
    @NotBlank(message = "Title is required") String title,
    @NotBlank(message = "Level is required") String level
) {}

@PostMapping
public LessonResponse create(@Valid @RequestBody CreateLessonRequest request) {
    return lessonService.create(request);
}
CODE,
                        'practice' => 'Create a request record with a required title and a suitable length rule. Send valid and invalid JSON and compare the response status.',
                    ],
                    [
                        'title' => '8. Dependency injection and controller-service-repository layers',
                        'explanation' => 'Spring creates and connects application objects called beans. Dependency injection means a class receives the objects it needs rather than constructing hidden dependencies with new. Keep the controller focused on HTTP: read the request and choose a response. Put application rules in a service. Put persistence operations behind a repository. Constructor injection makes dependencies visible, supports immutable fields and makes classes easier to test. Do not add a layer just for ceremony; each layer should have a clear job.',
                        'example' => <<<'CODE'
@Service
public class LessonService {
    private final LessonRepository repository;

    public LessonService(LessonRepository repository) {
        this.repository = repository;
    }

    public List<Lesson> findAll() {
        return repository.findAll();
    }
}
CODE,
                        'practice' => 'Move a piece of application logic out of a controller into a service and inject the service through its constructor.',
                    ],
                    [
                        'title' => '9. Persist data with Spring Data JPA',
                        'explanation' => 'A database keeps information after the application stops. JPA maps Java entity objects to relational tables, while Spring Data can create common repository methods from an interface. Start locally with an in-memory database to learn the mapping, then use a real database and deliberate schema migrations for production. Keep API DTOs separate from entities so a database refactor does not accidentally change your public JSON contract. Use transactions around related writes and understand when they commit or roll back.',
                        'example' => <<<'CODE'
@Entity
public class Lesson {
    @Id @GeneratedValue
    private Long id;
    private String title;

    protected Lesson() {} // JPA needs a constructor
    public Lesson(String title) { this.title = title; }
    public Long getId() { return id; }
    public String getTitle() { return title; }
}

public interface LessonRepository extends JpaRepository<Lesson, Long> {}
CODE,
                        'practice' => 'Add the JPA and H2 dependencies, save one lesson through a repository, restart the app and observe how an in-memory database behaves.',
                    ],
                    [
                        'title' => '10. Predictable errors and API responses',
                        'explanation' => 'A useful API distinguishes success from failure with HTTP status codes: 200 for a successful read, 201 for a created resource, 400 for invalid input, 404 for a missing resource and 500 for an unexpected server problem. Do not return success-shaped text for failures. Handle expected domain cases deliberately and use a centralized exception handler when multiple endpoints need the same error shape. Never expose stack traces, SQL, secrets or internal implementation details to callers.',
                        'example' => <<<'CODE'
@RestControllerAdvice
public class ApiErrorHandler {
    @ExceptionHandler(LessonNotFoundException.class)
    ResponseEntity<Map<String, String>> notFound(LessonNotFoundException error) {
        return ResponseEntity.status(HttpStatus.NOT_FOUND)
            .body(Map.of("error", error.getMessage()));
    }
}
CODE,
                        'practice' => 'Make a request for a lesson ID that does not exist and return a clear 404 response with a small JSON error body.',
                    ],
                    [
                        'title' => '11. Test the application at more than one level',
                        'explanation' => 'A unit test checks one class with controlled dependencies; it is quick and helps verify a rule. A web slice test checks controller mapping and JSON behavior without starting every part of the application. An integration test checks that configured components work together. Spring Boot provides @SpringBootTest for loading an application context, and spring-boot-starter-test brings common test tools. Test both expected results and important edge cases; do not make every small test load the entire application.',
                        'example' => <<<'CODE'
@SpringBootTest
class StudyApiApplicationTests {
    @Test
    void applicationContextStarts() {
        // If Spring cannot build the application context, this test fails.
    }
}

// Run the suite with: ./mvnw test
CODE,
                        'practice' => 'Write a service unit test for one business rule and a web test that checks the status and JSON returned by an endpoint.',
                    ],
                    [
                        'title' => '12. Configuration, profiles and environment variables',
                        'explanation' => 'Configuration changes between a laptop, test environment and production. Keep non-secret defaults in application.properties or application.yml. Use profiles for environment-specific behavior, for example application-dev.properties. Supply passwords and API keys through environment variables or a secret manager, never commit real credentials. Bind related settings into a configuration-properties class as an application grows, so configuration has a clear type and can be validated at startup.',
                        'example' => <<<'CODE'
# application.properties
server.port=${PORT:8080}
spring.application.name=study-api

# Example environment variable (set outside source control)
SPRING_DATASOURCE_PASSWORD=use-a-local-secret

# Start with a profile when needed
java -jar app.jar --spring.profiles.active=dev
CODE,
                        'practice' => 'Move the server port to an environment variable with a safe local default. Confirm a production password is not present in any tracked file.',
                    ],
                    [
                        'title' => '13. Security basics for a backend API',
                        'explanation' => 'A working endpoint is not automatically a secure endpoint. Decide which routes are public and which require authentication. Use Spring Security rather than writing your own password checks, hash passwords with a suitable adaptive algorithm, validate authorization on the server and use HTTPS in production. CORS controls which browser origins may read responses; it is not user authentication. Keep dependencies updated, give the application only the permissions it needs, and avoid returning private fields in DTOs.',
                        'example' => <<<'CODE'
@Configuration
public class SecurityConfig {
    @Bean
    SecurityFilterChain appSecurity(HttpSecurity http) throws Exception {
        return http
            .authorizeHttpRequests(auth -> auth
                .requestMatchers("/health").permitAll()
                .anyRequest().authenticated())
            .httpBasic(Customizer.withDefaults())
            .build();
    }
}
CODE,
                        'practice' => 'Draw a route table that marks public and protected endpoints. Add Spring Security and verify an unauthenticated request is rejected before adding a real login flow.',
                    ],
                    [
                        'title' => '14. Build, run and deploy a Spring Boot application',
                        'explanation' => 'Before release, run the complete build so compilation and automated checks happen together. The Spring Boot build plugin creates an executable jar with the application and its required libraries. You can run that file with java -jar. A production plan also needs environment configuration, a database migration strategy, HTTPS termination, logs, health monitoring, backups and a rollback path. Start with a managed Java host or container platform and follow its current supported JDK instructions; a successful local run alone does not prove production readiness.',
                        'example' => "# Maven: build and run the executable jar\n./mvnw clean verify\njava -jar target/study-api-0.0.1-SNAPSHOT.jar\n\n# Gradle equivalent\n./gradlew clean build\njava -jar build/libs/study-api-0.0.1-SNAPSHOT.jar",
                        'practice' => 'Package your project, run the jar on a non-default port, document the environment variables it needs, and write down how you would restore the previous release.',
                    ],
                ],
                'faq' => [
                    ['q' => 'Should I learn Java before Spring Boot?', 'a' => 'Yes, learn basic Java classes, methods, collections, exceptions and a build tool first. The TCM Java guide gives you that foundation; then this guide shows how Spring Boot wires Java code into a backend application.'],
                    ['q' => 'Which Spring Boot version does this guide use?', 'a' => 'This guide follows Spring Boot 4.1.2, the latest stable release checked on 24 October 2026. Spring Boot 4.2.0-RC1 is a preview release. Check the official Spring documentation for updates before starting a project.'],
                    ['q' => 'What Java version do I need?', 'a' => 'Spring Boot 4.1 requires Java 17 or later and supports Java through 27. For a longer support window, Java 25 is an LTS release; confirm your libraries and hosting provider support the JDK you select.'],
                    ['q' => 'Is Spring Boot only for REST APIs?', 'a' => 'No. It can also serve HTML pages, run scheduled jobs, process messages and support other application styles. A REST API is a useful first project because its requests and responses are easy to inspect.'],
                ],
            ],
        ];
    }
}
