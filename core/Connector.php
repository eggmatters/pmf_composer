<?php

namespace core;

/**
 * This class will instigate and abstract the process of fetching persisted data.
 * What we wish to accomplish is to obtain formatted data from *any* connection.
 * 
 * Our connections, at this point will be relegated to databases and API's. Each model will,
 * by convention, have one connection type associated with it. The connections themselves 
 * will be descendants of one of two abstract classes: PDOConn, or APIConn. Each
 * will provide abstract methods as well as defined methods and properties for returning
 * formatted data. Formatted data will be defined by abstract methods shared by both classes
 * along the nature of toJSON, toXML, etc. 
 * 
 * Derived connection classes will follow the same naming convention of MVC. 
 *
 * @author meggers
 */
class Connector {
  //put your code here
}
