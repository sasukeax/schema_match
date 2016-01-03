package main;

/**
 * 创建一个解析xml接口
 * @author weapon
 *
 */
public interface XmlInterface {
	 
    /** 
    * 建立XML文档 
    * @param fileName 文件全路径名称 
    */
    public void createXml(String fileName); 
    /** 
    * 解析XML文档 
    * @param fileName 文件全路径名称 
    */
    public void parserXml(String fileName); 
}
