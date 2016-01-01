package main;

import java.io.File;
import javax.xml.XMLConstants;
import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBElement;
import javax.xml.bind.Unmarshaller;
import javax.xml.transform.stream.StreamSource;
import javax.xml.validation.SchemaFactory;
import org.nssd.JElem;

public class Test {
	public static void main(String[] args) {
		JAXBContext context;
		try {
			context = JAXBContext.newInstance(JElem.class);

			Unmarshaller shaller = context.createUnmarshaller();

			shaller.setSchema(SchemaFactory.newInstance(  
			        XMLConstants.W3C_XML_SCHEMA_NS_URI).newSchema(  
			        new File("src/myxsd.xsd")));
			
			JAXBElement<JElem> root = shaller.unmarshal(new StreamSource(new File("src/data.xml")),JElem.class);
			JElem jElem = root.getValue();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}
}
