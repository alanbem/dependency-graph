# Dependency Graph

[![Build Status](https://travis-ci.org/alanbem/dependency-graph.svg?branch=master)](https://travis-ci.org/alanbem/dependency-graph)
[![Coverage Status](https://coveralls.io/repos/alanbem/dependency-graph/badge.png)](https://coveralls.io/r/alanbem/dependency-graph)

This library is a fork of https://github.com/MikeRoetgers/dependency-graph with the following changes:

 - Renamed `DependencyManager` class to `Graph`
 - Renamed base namespace to `QuietFrog`
 - Hid some classes (e.g. `Operation`) in order to make library more generic.
 - Removed tags handling

# Documentation

This is a simple implementation of a dependency graph (directed acyclic graph). Define services and dependencies between them. The graph keeps track of all dependencies and gives you an order in which services can be executed. This is especially convenient if you are working with long-running tasks and you want to identify which services may be executed in parallel.
 
## Example

```php
$service1 = new YourService1();
$service2 = new YourService2();
$service3 = new YourService3();
$service4 = new YourService4();

$graph = new DependencyManager();
$graph->add($service1)->add($service2)->add($service3)->add($service4);

$graph->addDependency($service1, $service2);
$graph->addDependency($service1, $service3);
$graph->addDependency($service2, $service4);
$graph->addDependency($service3, $service4);
```
This definition results in the following graph:

```
      1
    /  \
   2    3
    \  /
     4
```

Ask the graph which dependencies can be resolved. When service has been executed, mark it as resolved and ask for new available services.

```php
$services = $graph->getUnresolvedDependencies(); // 1
$graph->markAsResolved($service1);
$services = $graph->getUnresolvedDependencies(); // 2 and 3
$graph->markAsResolved($service3);
$services = $graph->getUnresolvedDependencies(); // 2
$graph->markAsResolved($service2);
$services = $graph->getUnresolvedDependencies(); // 4
```

More complex graphs are possible.

```
  1     2
  |    / \
  3   4   5
   \ /    |
    6     7
    |
    8
```

## Acyclicity

The graph is acyclic, which means something like this is NOT allowed:

```php
$service1 = new YourService2();
$service2 = new YourService2();
$service3 = new YourService3();

$graph = new DependencyManager();
$graph->add($service1)->add($service2)->add($service3);

$graph->addDependency($service1, $service2);
$graph->addDependency($service2, $service3);
$graph->addDependency($service3, $service1);
```

```
   1
  / \
 2 – 3
```

Cycles will be detected when the graph is initialized. A CircularDependencyDetectedException will be thrown.
